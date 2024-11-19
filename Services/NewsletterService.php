<?php

namespace Piffy\Services;

use Piffy\Framework\Router;
use Piffy\Plugins\Newsletter\Controllers\NewsletterController;

class NewsletterService {

    public function __construct()
    {
        Router::route('/ajax/newsletter-eintragen/', function () {
            $controller = new NewsletterController();
            $controller->registerSubscriber();
        });

        Router::route('/newsletter/subscribe', function () {
            $controller = new NewsletterController();
            $controller->subscribe();
        });

        Router::route('/newsletter/unsubscribe', function () {
            $controller = new NewsletterController();
            $controller->unsubscribe();
        });

        Router::route('/newsletter/send/', function () {
            $controller = new NewsletterController();
            $controller->send();
        });
    }

}