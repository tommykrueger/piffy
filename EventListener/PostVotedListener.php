<?php

namespace Piffy\EventListener;

use Piffy\Framework\Cache;

class PostVotedListener
{
    public function __invoke($event): void
    {
        Cache::clear($event->getFileName());
    }
}