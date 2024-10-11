<?php

use Piffy\Framework\View;
use App\Collections\PostCollection;

if (!function_exists('postLink')) {

    function postLink(array $postIds = []): void
    {
        View::partial('related-link', ['ids' => $postIds]);
    }
}
