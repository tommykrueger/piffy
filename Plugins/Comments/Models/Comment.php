<?php

namespace Piffy\Plugins\Models;

use Piffy\Framework\Model;

class Comment extends Model
{
    private int $id = 0;

    /**
     * @var string
     */
    private string $status;

    public function __construct($id = 0)
    {
        parent::__construct();

        $this->id = $id;
        @session_start();
        unset($_SESSION['user_poll_data']);
    }

    public function getStatus(): string
    {
        return $this->status;
    }

}