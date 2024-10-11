<?php

use Piffy\Framework\View;

if (!function_exists('partial')) {
    function partial(string $path, mixed $data = null): void
    {
        View::partial($path, $data);
    }
}
