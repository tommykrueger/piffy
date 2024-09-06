<?php

namespace Piffy\Plugins\Newsletter\Models;

use const Plugins\Newsletter\Models\PLUGINS_DIR;

class SubscriberService
{

    protected $data;

    protected $file = null;

    public function __construct()
    {
        $this->loadData();
    }

    private function loadData()
    {
        $this->file = APP_DIR . '/data/' . md5($this->email . time()) . '.json';
        $fileData = @file_get_contents($this->file);
        if ($fileData) {
            $this->data = json_decode($fileData);
        }
    }

    public function findByToken($token)
    {
        $subscribers = array_values(array_filter($this->data, function ($subscriber) use ($token) {
            return $token === $subscriber->token;
        }));

        if (count($subscribers) === 1) {
            $subscriber = $subscribers[0];
            return (object)$subscriber;
        }
        return false;
    }

    public function save()
    {
        $this->file = PLUGINS_DIR . '/newsletter/data/' . md5($this->data['email'] . time()) . '.json';
        @file_put_contents($this->file, json_encode($this->data));
    }

    public function exists($email)
    {
        var_dump($this->data);
        exit;
        $subscribers = array_values(array_filter($this->data, function ($subscriber) use ($email) {
            return $email === $subscriber->email;
        }));
        return (count($subscribers) >= 1);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $d = [
            'created' => date('Y-m-d H:i:s')
        ];

        $data = array_merge($d, $data);
        $this->data = $data;
    }

}