<?php

namespace Piffy\Framework;

class Validator
{
    public static function isEmail(?string $email): bool
    {
        return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function required(?string $agb): bool
    {
        return !empty($agb);
    }
}

