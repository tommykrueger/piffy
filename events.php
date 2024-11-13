<?php

use Piffy\Enum\Events;
use Piffy\EventListener\PostVotedListener;

return [

    Events::POST_VOTED => [
        PostVotedListener::class
    ],

];