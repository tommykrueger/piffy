<?php

namespace Piffy\Plugins\Newsletter;

use Piffy\Plugins\Newsletter\Collections\SubscriberCollection;
use Piffy\Plugins\Newsletter\Models\Email;

// include_once('Collections/SubscriberCollection.php');

// include_once('Models/email.php');
include_once('Models/Subscriber.php');

class NewsletterAPI
{
    protected $subscribers = [];

    public function __construct()
    {
        $subscriberCollection = SubscriberCollection::getInstance();
        $this->subscribers = $subscriberCollection::getAll();
    }

    public function run(): bool
    {
        if (empty($this->subscribers)) {
            echo 'no subscribers found';
            return false;
        }

        $subscribers = array_values(array_filter($this->subscribers, function ($subscriber) {
            return (isset($subscriber->activated) && 1 === $subscriber->activated);
        }));

        foreach ($subscribers as $subscriber) {
            $email = new Email((object)[
                'recipient' => $subscriber->email,
                'subject' => 'Newsletter #' . date('Y-m-d'),
                'emailTemplate' => 'newsletter',
            ]);

            if ($email->send()) {
                echo '<p>newsletter sent to: ' . $subscriber->email . '<br/></p>';
            }
        }

        return true;
    }

}