<?php

use Piffy\EventListener\PostVotedListener;
use Piffy\Services\EventService;

return [

    EventService::POST_VOTED => [
        PostVotedListener::class
    ],

];