<?php

namespace Piffy\Models;

use Piffy\Collections\PostCollection;
use Piffy\Framework\Model;
use Piffy\Plugins\Newsletter\Models\Email;
use Piffy\Plugins\StarRating\Models\Exception;
use stdClass;

class StarRating extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
}