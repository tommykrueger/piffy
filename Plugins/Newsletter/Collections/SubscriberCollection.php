<?php

namespace Piffy\Plugins\Newsletter\Collections;

class SubscriberCollection
{

    protected static $_instance = null;

    protected static $data = null;

    private function __construct()
    {
        self::$data = [];

        $dir = PLUGINS_DIR . '/newsletter/data/';
        $temp_files = glob($dir . '*.json');

        foreach ($temp_files as $file) {
            $filename = str_replace('.json', '', basename($file));
            $data = json_decode(file_get_contents($file), true);
            array_push(self::$data, (object)$data);
        }
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function getAll()
    {
        return self::$data;
    }

    public static function getActivatedSubscribers($limit = 10): array
    {
        return array_values(array_filter(self::$data, function ($subscriber) {
            return $subscriber->activated === 1;
        }));
    }
}