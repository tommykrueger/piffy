<?php

namespace Piffy\Plugins\Newsletter\Models;

use Piffy\Framework\Model;
use Piffy\Traits\CryptographyTrait;

class Subscriber extends Model
{
    use CryptographyTrait;

    public const STATUS_SUBSCRIBED = 'subscribed';

    public const STATUS_UNSUBSCRIBED = 'unsubscribed';

    public const STATUS_REGISTERED = 'registered';

    private string $dirName = USERDATA_DIR . DS . 'subscribers';

    /*
    public function __get($property): ?string
    {
        if ($property === 'email') {
            return $this->decrypt($this->data[$property]);
        }
        return $this->data[$property] ?? null;
    }
    */

    public function save(): void
    {
        //$this->file = PLUGINS_DIR . '/newsletter/data/' . md5($this->data['email'] . time()) . '.json';
        //@file_put_contents($this->file, json_encode($this->data));

        if (!is_dir($this->dirName)) {
            mkdir($this->dirName);
        }

        $fileName = 'subscriber_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.json';
        $file = $this->_data['file'] ?? $this->dirName . DS . $fileName;

        $this->_data['created'] = date('Y-m-d_H-i-s');
        $this->_data['updated'] = date('Y-m-d_H-i-s');

        @file_put_contents($file, json_encode($this->_data));
    }

    public function set(string $key, string $value): Subscriber
    {
        $this->_data[$key] = $value;
        return $this;
    }

    public function getEmail(): ?string
    {
        if ($this->_data['email']) {
            $this->_data['email'] = $this->decrypt($this->_data['email']);
        }
        return null;
    }

}