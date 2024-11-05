<?php

namespace Piffy\Services;

use Piffy\Controllers\StarRatingController;
use Piffy\Framework\Router;

class StarRatingService
{
    public function __construct()
    {
        Router::route('/ajax/star-rating/', function () {
            $instance = new StarRatingController();
            $instance->save();
        });
    }
}

