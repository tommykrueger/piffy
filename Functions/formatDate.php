<?php

if (!function_exists('formatDate')) {
    function formatDate(string $format, mixed $datetime): string
    {
        $date = date($format, strtotime($datetime));

        return str_replace(
            ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
            $date
        );
    }
}

