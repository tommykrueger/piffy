<?php

use App\Collections\PostCollection;
use App\Controllers\PostController;
use Piffy\Framework\Router;

$posts = PostCollection::getInstance()->getAll();
if ($posts) {
    $postController = new PostController();
    foreach ($posts as $post) {

        Router::route($post->slug, function() use ($postController, $post) {

            $slug = $post->slug;
            if (substr($slug, -1, 1) === '/') {
                $slug = substr($slug, 0, -1);
            }

            header('Location: '. DOMAIN . $slug . '.html', true, 301);
            exit;

            // $postController->render($post);
        });


        $slug = $post->slug;
        if (substr($slug, -1, 1) === '/') {
            $slug = substr($slug, 0, -1);
        }

        Router::route($slug . '.html', function() use ($postController, $post) {
            $postController->render($post);
        });
    }
}

Router::route('/ajax/vote/(\d+)', function($id){
    $controller = new PostController();
    $controller->postVote($id);
});

Router::route('/ajax/post/like/(\d+)', function($id){
    $controller = new PostController();
    $controller->addListLike($id);
});

Router::route('/ajax/pageview/(\d+)', function($id){
    $controller = new PostController();
    $controller->savePageView($id);
});
