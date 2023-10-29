<?php

namespace App\Collections;

use App\Models\Page;

class PageCollection
{

    private static PageCollection $_instance;

    private static array $data = [];

    public function __construct()
    {
        self::$data = self::load();
    }

    public static function getInstance(): PageCollection
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function load()
    {
        self::$data = include(APP_DIR . 'data/pages.php');

        for ($i = 0; $i < count(self::$data); $i++) {
            self::$data[$i] = (object)self::$data[$i];

            if (!empty(self::$data[$i]->image)) {
                $image = self::$data[$i]->image;
                self::$data[$i]->image_placeholder = DOMAIN . '/app/public/img/logo.svg';
                //self::$data[$i]->image = self::$postImage->getImageSizeUrl($image, 600, 338);
                //self::$data[$i]->image2x = self::$postImage->getImageSizeUrl($image, 1200, 676);
            }
        }

        return self::$data;
    }

    public static function getData()
    {
        return self::$data;
    }

    /**
     * Get a page by its id
     * @param int $id
     * @return array
     */
    public static function getPageById(int $id = 0): stdClass
    {
        $data = array_values(array_filter(self::$data, function ($page) use ($id) {
            return ($page->id === $id);
        }));
        return (object)$data[0] ?? (object)[];
    }


    public static function getPagesById(array $ids = []): array
    {
        $data = array_values(array_filter(self::$data, function ($page) use ($ids) {
            return in_array($page->id, $ids);
        }));
        return $data;
    }


    public static function getPageByName(string $name): Page
    {
        $data = array_values(array_filter(self::$data, function ($d) use ($name) {
            return $name === $d->name;
        }));

        return new Page($data); //(object)$data[0] ?? (object)[];
    }


    /**
     * @param int $limit
     * @return array
     */
    public static function getPostsOrderedByDate($limit = 10000): array
    {
        $data = self::getData();

        usort($data, function ($a, $b) {
            return $a->created > $b->created;
        });

        $data = array_reverse($data);
        $data = array_slice($data, 0, $limit);
        return $data;
    }


    // @todo
    public static function getRandom($limit, $excludeIds = [])
    {
        $data = self::$data;
        shuffle($data);
        return array_slice($data, 0, $limit);
    }

}