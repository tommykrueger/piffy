<?php

namespace Piffy\Models;

use Piffy\Framework\Model;
use Piffy\Plugins\Comments\Models\Enum\CommentStatus;

class Comment extends Model
{
    public string $name = '';

    public string $message = '';

    private string $status = CommentStatus::PENDING;

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