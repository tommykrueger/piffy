<?php

namespace Piffy\Framework;

class Cache
{
    private static bool $isActive = false;

    private static ?string $cacheFile = null;

    private static int $defaultCacheTime = 60 * 60 * 12;  // half day cache

    private function __construct()
    {
    }

    public static function start(string $url, ?int $cacheTime): void
    {
        if (!defined('CACHE_ACTIVE')) {
            Error::warning('Constant CACHE_ACTIVE not set');
            return;
        }

        if (false === CACHE_ACTIVE) {
            return;
        }

        //$time_elapsed_secs = microtime(true) - self::$start;
        //echo '<!-- rendered in ' . round($time_elapsed_secs, 4) . ' seconds -->';

        //$url = $_SERVER["SCRIPT_NAME"];
        $urlParts = explode('/', $url);
        $file = implode('_', $urlParts);

        if ($file == '_') {
            $file = '_homepage';
        }

        self::$cacheFile = BASE_DIR . '/public/cache/file_' . $file . '.html';
        $cacheTime = $cacheTime ?? self::$defaultCacheTime;

        // Serve from the cache if it is younger than $cachetime
        if (file_exists(self::$cacheFile) && time() - $cacheTime < filemtime(self::$cacheFile)) {
            readfile(self::$cacheFile);
            echo "<!-- cached copy @ " . date('H:i:s', filemtime(self::$cacheFile)) . " -->\n";
            Debug::endTime();
            exit;
        }

        ob_start(); // Start the output buffer
    }

    public static function end(): void
    {
        if (!defined('CACHE_ACTIVE')) {
            Error::warning('Constant CACHE_ACTIVE not set');
            return;
        }

        if (false === CACHE_ACTIVE) {
            return;
        }

        if (self::$cacheFile) {
            $cached = fopen(self::$cacheFile, 'w');
            $data = self::minifyHTML(ob_get_contents());
            // $data = ob_get_contents();
            fwrite($cached, $data);
            fclose($cached);
            ob_end_flush(); // Send the output to the browser
        }
    }

    public static function clear($url): void
    {
        if ($url) {
            $url = str_replace([DOMAIN, 'http://', 'https://', '/'], '', $url);
            $url .= '.html';

            $file = BASE_DIR . 'public/cache/' . 'file_' . $url;

            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * See https://exceptionshub.com/how-to-minify-php-page-html-output.html
     *
     * @param $data
     * @return string|string[]|null
     */
    private static function minifyHTML($data)
    {

        //remove redundant (white-space) characters
        $replace = array(
            //remove tabs before and after HTML tags
            '/\>[^\S ]+/s' => '>',
            '/[^\S ]+\</s' => '<',
            //shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
            '/([\t ])+/s' => ' ',
            //remove leading and trailing spaces
            '/^([\t ])+/m' => '',
            '/([\t ])+$/m' => '',
            // remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
            '~//[a-zA-Z0-9 ]+$~m' => '',
            //remove empty lines (sequence of line-end and white-space characters)
            '/[\r\n]+([\t ]?[\r\n]+)+/s' => "\n",
            //remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
            '/\>[\r\n\t ]+\</s' => '><',
            //remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
            '/}[\r\n\t ]+/s' => '}',
            '/}[\r\n\t ]+,[\r\n\t ]+/s' => '},',
            //remove new-line after JS's function or condition start; join with next line
            '/\)[\r\n\t ]?{[\r\n\t ]+/s' => '){',
            '/,[\r\n\t ]?{[\r\n\t ]+/s' => ',{',
            //remove new-line after JS's line end (only most obvious and safe cases)
            '/\),[\r\n\t ]+/s' => '),',
            //remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
            '~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4', //$1 and $4 insert first white-space character found before/after attribute
        );

        return preg_replace(array_keys($replace), array_values($replace), $data);
    }
}