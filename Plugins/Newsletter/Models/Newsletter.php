<?php

namespace Piffy\Plugins\Newsletter\Models;

use Piffy\Plugins\Newsletter\Models\Subscriber;

include('Subscriber.php');
include('Email.php');

class Newsletter
{
    protected array $response = [];

    public function __construct()
    {
        // load subscriber list
        // load all data
        // create a newsletter from the data
        // send the newsletter for every subscriber in chunks
    }

    public static function getNonsense(): string
    {
        @session_start();
        $nonsense = sha1('th15_I5_@_great_WÄBSITE_' . time());
        $nonsense = substr($nonsense, 0, 32);
        $_SESSION['nonsense'] = $nonsense;
        return $nonsense;
    }

    public function processForm(): void
    {
        @session_start();

        $errors = [];
        $requestData = $this->getRequestData();
        if (empty($requestData)) {
            $this->respond();
        }

        # client tries to cheat ahhhh
        if (isset($_SESSION['nonsense']) && strlen($_SESSION['nonsense']) === 32) {
            // $this->respond();
        }

        # wrong email
        if (!filter_var($requestData->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = [
                'field' => 'email',
                'message' => 'Email falsch'
            ];
        }

        if (!empty($errors)) {
            $this->response = [
                'status' => false,
                'errors' => $errors
            ];

        } else {

            $subscriber = new Subscriber();
            if ($subscriber->exists($requestData->email)) {
                $this->response = [
                    'status' => false,
                    'message' => 'Diese Email-Adresse existiert bereits.'
                ];
                $this->respond();
            }

            $token = sha1('user_activation_' . time());

            $subscriber->setData([
                'email' => $requestData->email,
                'token' => $token,
                'activated' => 1
            ]);
            $subscriber->save();

            $email = new Email((object)[
                'recipient' => $requestData->email,
                'subject' => 'Newsletter Bestätigung <noreply@lachvegas.de>',
                'emailTemplate' => 'add_newsletter_subscriber',
                'emailData' => [
                    'link' => DOMAIN . '/newsletter-aktivieren?token=' . $token
                ]
            ]);

            $email->send();

            $this->response = [
                'status' => true,
                'message' => 'Bitte schauen Sie in Ihr E-Mail-Postfach und bestätigen Sie Ihren Newsletter durch Klick auf den Link.'
            ];

            // also send email to the admin
            $email = new Email((object)[
                'recipient' => 'newsletter@lachvegas.de',
                'subject' => 'Neuer Newsletter Abonnent' . $requestData->email,
                'emailTemplate' => 'admin_add_newsletter_subscriber',
                'emailData' => [
                    'email' => $requestData->email
                ]
            ]);

            $email->send();
        }

        $this->respond();
    }

    /**
     * @return array|mixed
     */
    private function getRequestData()
    {
        if (isset($_REQUEST['data'])) {
            return (object)json_decode($_REQUEST['data']);
        }
        return [];
    }

    private function respond()
    {
        echo json_encode($this->response);
        exit;
    }

    public function activateSubscription()
    {
        if (isset($_REQUEST['token'])) {

            $token = $_REQUEST['token'];

            // find the token of the subscription;
            $subscriber = Subscriber::findByToken($token);

            if (isset($subscriber->id)) {
                render('newsletter_activation_success', []);
                exit;
            }
        }

        echo 'Token mismatch!';
        exit;
    }

}