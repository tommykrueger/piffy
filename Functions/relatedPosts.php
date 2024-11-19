<?php

use Piffy\Framework\View;

if (!function_exists('relatedLinks'))
{
    function relatedLinks(array $postIds = []): void
    {
        View::partial('related-links', ['ids' => $postIds]);
    }
}
