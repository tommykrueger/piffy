<?php

namespace Piffy\EventListener;

use Piffy\Framework\Cache;

class NewsletterSubscribedListener
{
    public function __invoke($event): void
    {
        $event->getEmail();
    }
}