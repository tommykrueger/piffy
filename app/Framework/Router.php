<?php

namespace App\Framework;

use App\Collections\PageCollection;
use App\Controllers\PageController;

class Router
{

    private static $routes = array();

    private static $redirects = array();

    private function __construct()
    {
    }

    private function __clone()
    {
    }


    public static function route($pattern, $callback)
    {
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
        self::$routes[$pattern] = $callback;
    }


    public static function execute($url)
    {
        // $url = rtrim($url,'/');
        if ($url == '') {
            $url = '/';
        }

        if (isset(self::$redirects[$url])) {
            // var_dump('redirect exists for ' . $url);
            $pattern = self::$redirects[$url];
            //var_dump('redirect pattern ' . $pattern);
            self::redirect(DOMAIN . $pattern);
        }

        $lastChar = substr($url, -1);
        if ($lastChar !== '/' && false === stripos($url, '/ajax') && false === stripos($url, '/suche') && false === stripos($url, 'sitemap')) {
            Router::redirect($url . '/');
        }

        Cache::start($url);

        foreach (self::$routes as $pattern => $callback) {

            if (strpos($url, "?") !== false) {
                $get_params = substr($url, strpos($url, "?"));
            }

            if (isset($get_params) && !empty($get_params)) {
                $url = str_replace($get_params, "", $url);
            }

            // $url = rtrim($url,'/');
            if ($url == '') {
                $url = '/';
            }

            if (preg_match($pattern, $url, $params) === 1) {
                array_shift($params);
                return call_user_func_array($callback, array_values($params));
            }
        }

        header("HTTP/1.1 404 Not Found");
        (new PageController)->render('404', PageCollection::getInstance()::getPageByName('404'));
        exit;
    }

    public static function addRedirect($slug, $redirect)
    {
        self::$redirects[$slug] = $redirect;
    }

    public static function getRedirects()
    {
        return self::$redirects;
    }

    /**
     * @param $slug
     * @param int $type
     * @return void
     */
    public static function redirect($slug, int $type = 301): void
    {
        $url = DOMAIN . $slug;
        header("Location: " . $url, true, $type);
        exit;
    }
}