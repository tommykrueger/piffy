<?php

namespace Piffy\Services;

use Error;
use Exception;
use Piffy\Plugins\Newsletter\Models\Email;

class EmailService
{
    public function __construct()
    {

    }

    public function send(): bool
    {
        try {
            $emailData['id'] = 0;
            $emailData['post'] = 0;
            $emailData['likes'] = 1;
            $email = new Email((object)[
                'recipient' => 'info@lachvegas.de',
                'emailData' => (object)$emailData,
                'subject' => 'New Post Like #' . date('Y-m-d'),
                'emailTemplate' => 'post_like',
            ]);
            $email->send();

            // Cache::clear($post->getSlug());
        } catch (Exception $e) {

        }

        return true;
    }
}

