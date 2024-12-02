<?php

use Piffy\Enum\Events;
use Piffy\EventListener\CommentPostedListener;
use Piffy\EventListener\NewsletterSubscribedListener;
use Piffy\EventListener\PostVotedListener;

return [

    Events::COMMENT_POSTED => [
        CommentPostedListener::class
    ],

    Events::POST_VOTED => [
        PostVotedListener::class
    ],

    Events::NEWSLETTER_SUBSCRIBED => [
        NewsletterSubscribedListener::class
    ],

];