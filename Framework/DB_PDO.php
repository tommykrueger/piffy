<?php

namespace Piffy\Framework;

use PDO;

class DB_PDO extends PDO
{
    public function __construct()
    {
        $dns = DB_DRIVER .
            ':host=' . DB_HOST .
            ';port=' . DB_PORT .
            ';dbname=' . DB_NAME;

        parent::__construct($dns, DB_USER, DB_PASS);
    }
}
