<?php

namespace Piffy\Services;

use Piffy\Controllers\CommentController;
use Piffy\Framework\Router;

class CommentService {

    public function __construct()
    {
        Router::route('/ajax/comment/(\d+)', function ($id) {
            $controller = new CommentController();
            $controller->addComment($id);
        });

        Router::route('/activate-comment/(\d+)/(\d+)', function ($id1, $id) {
            $controller = new CommentController();
            $controller->activateComment($id1, $id);
        });
    }

}