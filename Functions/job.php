<?php

use Piffy\Framework\View;

if (!function_exists('job')) {

    function job($post, $data = null): void
    {
        View::job($post, $data);
    }
}
