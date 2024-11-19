<?php

use Piffy\Enum\Events;
use Piffy\EventListener\NewsletterSubscribedListener;
use Piffy\EventListener\PostVotedListener;

return [

    Events::POST_VOTED => [
        PostVotedListener::class
    ],

    Events::NEWSLETTER_SUBSCRIBED => [
        NewsletterSubscribedListener::class
    ],

];