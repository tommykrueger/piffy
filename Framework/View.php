<?php

namespace Piffy\Framework;

class View
{

    private function __construct()
    {
    }

    public static function render($name, $data = null): void
    {
        // $data = $data ? (object)$data : new stdClass();

        if ($name === '404') {
            $data->title = '404';
        }

        include_once(VIEWS_DIR . $name . '.php');
        Cache::end();
        //exit;
    }

    public static function view($name, $data = null)
    {
        $data = (object)$data;
        $file = VIEWS_DIR . $name . '.php';
        if (file_exists($file)) {
            include($file);
        }
    }

    public static function partial(string $name, mixed $data = null): void
    {
        $data = (object)$data;
        $file = PARTIALS_DIR . $name . '.php';
        if (file_exists($file)) {
            include($file);
        }
    }

    public static function news($id, $data = null)
    {
        $data = (object)$data;
        $file = VIEWS_DIR . 'news/' . $id . '.php';
        if (file_exists($file)) {
            include($file);
        } else {
            echo 'Inhalt folgt..';
        }
    }

    public static function post($file, $data = null): void
    {
        // $data = (object)$data;
        $fileName = VIEWS_DIR . 'posts/' . $file . '.php';
        $fileId = VIEWS_DIR . 'posts/' . $file . '.php';

        if (is_file($fileName)) {
            include($fileName);
        } elseif (is_file($fileId)) {
            include($fileName);
        } else {
            echo 'Inhalt folgt..';
        }
    }

    public static function job($id, $data = null)
    {
        $data = (object)$data;
        $file = VIEWS_DIR . '/jobs/' . $id . '.php';
        if (file_exists($file)) {
            include($file);
        }
    }

    public static function person($name, $data = null)
    {
        $data = (object)$data;
        $file = VIEWS_DIR . '/persons/' . $name . '.php';
        if (file_exists($file)) {
            include($file);
        }
    }
}

