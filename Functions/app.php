<?php

use App\App;

if (!function_exists('app')) {
    function app(string $className): mixed
    {
        var_dump($className);
        return App::getServiceInstance($className);
    }
}

