<?php

namespace Piffy\Plugins\Newsletter\Controllers;

use Piffy\Framework\Request;
use Piffy\Framework\Validator;
use Piffy\Plugins\Newsletter\Collections\SubscriberCollection;
use Piffy\Plugins\Newsletter\Models\Email;
use Piffy\Plugins\Newsletter\Models\Subscriber;
use Piffy\Traits\CryptographyTrait;

class NewsletterController
{
    use CryptographyTrait;

    protected ?array $subscribers = [];

    protected array $response = [];

    protected SubscriberCollection $subscriberCollection;

    public function __construct()
    {
        $this->subscriberCollection = SubscriberCollection::getInstance();
    }

    public function send(): bool
    {
        $this->subscribers = $this->subscriberCollection->findByStatus(Subscriber::STATUS_SUBSCRIBED);

        if (empty($this->subscribers)) {
            echo 'no subscribers found';
            return false;
        }

        foreach ($this->subscribers as $subscriber) {
            $email = new Email((object)[
                'recipient' => $this->decrypt($subscriber->email),
                'subject' => 'LachVegas Newsletter vom ' . date('d.m.Y'),
                'emailTemplate' => 'newsletter',
            ]);
            if ($email->send()) {
                echo '<p>newsletter sent to: ' . $subscriber->email . '<br/></p>';
            }
        }

        return true;
    }

    public function registerSubscriber(): void
    {
        Request::all();

        $email = Request::get('email');
        $agb = Request::get('agb');

        $isEmail = Validator::isEmail($email);
        $isChecked = Validator::required($agb);

        # client tries to cheat ahhhh
        if (isset($_SESSION['nonsense']) && strlen($_SESSION['nonsense']) === 32) {
            // @todo
        }

        //var_dump($isChecked);


        if (!$isEmail) {
            $this->response = [
                'status' => false,
                'field' => 'email',
                'errors' => 'Email falsch'
            ];
            $this->respond();
        }

        if (!$isChecked) {
            $this->response = [
                'status' => false,
                'field' => 'agb',
                'errors' => 'Agb falsch'
            ];
            // $this->respond();
        }

        $entries = $this->subscriberCollection->findByEmail($email);
        //var_dump($entries);

        if ($entries) {
            $this->response = [
                'status' => false,
                'field' => 'email',
                'errors' => 'Email-Adresse existiert bereits'
            ];
            // $this->respond();
        }

        $token = sha1('user_activation_' . time());
        $subscriber = new Subscriber([
            'email' => $this->encrypt($email),
            'agb' => $agb,
            'status' => Subscriber::STATUS_REGISTERED,
            'token' => $token,
        ]);
        $subscriber->save();


        $mailer = new Email((object)[
            'recipient' => $email,
            'subject' => 'Newsletter Bestätigung <noreply@lachvegas.de>',
            'emailTemplate' => 'add_newsletter_subscriber',
            'emailData' => [
                'link' => DOMAIN . '/newsletter/subscribe?token=' . $token
            ]
        ]);

        if ($mailer->send()) {
            $this->response = [
                'status' => true,
                'message' => 'Bitte schauen Sie in Ihr E-Mail-Postfach und bestätigen Sie Ihren Newsletter durch Klick auf den Link.'
            ];
        }

        $this->respond();

        // dump($email, $isEmail, $isChecked);
        // event();
    }

    public function respond(): void
    {
        echo json_encode($this->response);
        exit;
    }

    public function subscribe(): void
    {
        Request::all();
        $token = Request::get('token');

        $subscriber = $this->subscriberCollection->findByToken($token);
        $subscriber->set('status', Subscriber::STATUS_SUBSCRIBED);
        $subscriber->save();
    }

    public function unsubscribe(): void
    {
        Request::all();
        $token = Request::get('token');

        $subscriber = $this->subscriberCollection->findByToken($token);
        $subscriber->set('status', Subscriber::STATUS_UNSUBSCRIBED);
        $subscriber->save();
    }

}