<?php

namespace Piffy\Plugins\Newsletter\Collections;

use Piffy\Framework\Collection;
use Piffy\Plugins\Newsletter\Models\Subscriber;
use Piffy\Traits\CryptographyTrait;

class SubscriberCollection extends Collection
{
    use CryptographyTrait;

    protected static ?SubscriberCollection $_instance = null;

    protected static ?array $data = null;

    public string $model = Subscriber::class;

    public function __construct()
    {
        self::$data = [];

        $dir = USERDATA_DIR . DS . 'subscribers' . DS;

        $temp_files = glob($dir . '*.json');
        $model = $this->getModel();

        foreach ($temp_files as $file) {
            // $filename = str_replace('.json', '', basename($file));
            $data = json_decode(file_get_contents($file), true);

            $data['file'] = $file;

            //if ($data['email']) {
                //$data['email'] = $this->decrypt($data['email']);
            //}

            self::$data[] = new $model($data);
        }
    }

    public function findByStatus(string $status = Subscriber::STATUS_SUBSCRIBED): array
    {
        return array_values(array_filter(self::$data, function ($subscriber) use ($status) {
            return $subscriber->status === $status;
        }));
    }

    public function findByEmail(?string $email): array
    {
        return array_values(array_filter(self::$data, function ($subscriber) use ($email) {
            return $subscriber->email === $email;
        }));
    }

    public function findByToken($token): ?Subscriber
    {
        $subscribers = array_values(array_filter(self::$data, function ($subscriber) use ($token) {
            return $token === $subscriber->token;
        }));

        if (count($subscribers) === 1) {
            return $subscribers[0];
        }
        return null;
    }

    public function exists($email): bool
    {
        $subscribers = array_values(array_filter(self::$data, function ($subscriber) use ($email) {
            return $email === $subscriber->email;
        }));
        return (count($subscribers) >= 1);
    }
}