<?php

namespace Piffy\EventListener;

use Piffy\Framework\Cache;

class CommentPostedListener
{
    public function __invoke($event): void
    {
        Cache::clear($event->getFileName());
    }
}