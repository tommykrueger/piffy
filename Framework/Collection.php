<?php

namespace Piffy\Framework;

// https://refactoring.guru/design-patterns/singleton/php/example#example-1

class Collection
{
    /**
     * The actual singleton's instance almost always resides inside a static
     * field. In this case, the static field is an array, where each subclass of
     * the Singleton stores its own instance.
     */
    private static array $instances = [];

    /**
     * @var string
     */
    public string $source = '';

    /**
     * @var array
     */
    public array $_data = [];

    /**
     * @var array
     */
    public array $_rawData = [];

    /**
     * @var string
     */
    public string $model;

    public function __construct()
    {
        if (isset($this->source)) {
            $this->loadData();
        }
    }

    /**
     * @return void
     */
    public function loadData(): void
    {
        $source = $this->getSource();
        if (!is_file($source)) {
            return;
        }

        $this->_rawData = include($source);

        foreach ($this->_rawData as $i => $iValue) {
            $model = $this->getModel();
            $this->_data[] = new $model($this->_rawData[$i]);

            /*
            if (!empty(self::$data[$i]->image)) {
                $image = self::$data[$i]->image;
                self::$data[$i]->image_placeholder = DOMAIN . '/app/public/img/logo.svg';
                //self::$data[$i]->image = self::$postImage->getImageSizeUrl($image, 600, 338);
                //self::$data[$i]->image2x = self::$postImage->getImageSizeUrl($image, 1200, 676);
            }
            */
        }
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->_data;
    }

    /**
     * @return array
     */
    public function getAllRaw(): array
    {
        return $this->_rawData;
    }

    /**
     * @param int $id
     * @return Model
     */
    public function getById(int $id): object
    {
        $data = array_values(array_filter($this->_data, function ($d) use ($id) {
            return ($d->id === $id);
        }));
        return (object)$data[0] ?? (object)[];
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getByIds(array $ids = []): array
    {
        return array_values(array_filter($this->_data, function ($d) use ($ids) {
            return in_array($d->id, $ids, true);
        }));
    }

    public function getByName(string $name): Model
    {
        $data = array_values(array_filter($this->_data, function ($d) use ($name) {
            return ($d->name === $name);
        }));
        return $data[0];
    }
}