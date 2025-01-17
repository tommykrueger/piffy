<?php

use Piffy\Framework\View;
use Piffy\Collections\PostCollection;

if (!function_exists('postLink')) {

    function postLink(array $postIds = []): void
    {
        View::partial('related-links', ['ids' => $postIds]);
    }
}
