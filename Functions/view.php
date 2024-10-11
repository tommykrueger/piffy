<?php

use Piffy\Framework\View;

if (!function_exists('view')) {
    function view($post, $data = null): void
    {
        View::render($post, $data);
    }
}
