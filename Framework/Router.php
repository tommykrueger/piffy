<?php

namespace Piffy\Framework;

use Piffy\Exceptions\RouteNotFoundException;

class Router
{
    private static ?Router $_instance = null;

    private static array $routes = array();

    private static array $redirects = array();

    private function __construct()
    {
        self::loadRedirects();
    }

    private function loadRedirects(): void
    {
        $file = APP_DIR . 'redirects.php';
        if (is_file($file)) {
            self::$redirects = include_once($file);
        }
    }

    public static function getInstance(): Router
    {
        if (null === self::$_instance) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    public static function route($pattern, $callback): void
    {
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
        self::$routes[$pattern] = $callback;
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * @throws RouteNotFoundException
     */
    public static function execute($url)
    {
        // $url = rtrim($url,'/');
        if ($url == '') {
            $url = '/';
        }

        // check if there is a redirect for that route
        self::checkRedirects($url);

        $lastChar = substr($url, -1);
        if ($lastChar !== '/' && false === stripos($url, '/ajax') && false === stripos($url, '/suche') && false === stripos($url, 'sitemap')) {
            // Router::redirect($url . '/');
        }

        foreach (self::$routes as $pattern => $callback) {

            if (str_contains($url, "?")) {
                $getParams = substr($url, strpos($url, "?"));
            }

            if (!empty($getParams)) {
                $url = str_replace($getParams, "", $url);
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

        // header("HTTP/1.1 404 Not Found");
        throw new RouteNotFoundException('Route ' . $url . ' not found');
        exit;
    }

    /**
     * @param string $url
     * @return void
     */
    private static function checkRedirects(string $url): void
    {
        $connectedRedirects = array_filter(self::$redirects, function ($redirect) use ($url) {
            return isset($redirect['from']) && $redirect['from'] === $url;
        });

        if (empty($connectedRedirects)) {
            return;
        }

        foreach ($connectedRedirects as $connectedRedirect) {
            self::redirect($connectedRedirect['to'], $connectedRedirect['http_status']);
        }

        if (isset(self::$redirects[$url])) {
            // var_dump('redirect exists for ' . $url);
            $pattern = self::$redirects[$url];
            //var_dump('redirect pattern ' . $pattern);
            self::redirect(DOMAIN . $pattern);
        }
    }

    /**
     * @param $slug
     * @param int $type
     */
    public static function redirect($slug, int $type = 301): void
    {
        $url = str_contains($slug, 'http') ? $slug : DOMAIN . $slug;
        header("Location: " . $url, true, $type);
        exit;
    }

    public static function addRedirect($slug, $redirect): void
    {
        self::$redirects[$slug] = $redirect;
    }

    public static function getRedirects(): array
    {
        return self::$redirects;
    }

    private function __clone()
    {
    }
}