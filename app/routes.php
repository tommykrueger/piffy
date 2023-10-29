<?php

use App\Controllers\AjaxController;
use App\Controllers\CategoryController;
use App\Controllers\PageController;
use App\Controllers\PostController;
use App\Framework\Router;

use App\Collections\PageCollection;

$pages = PageCollection::getInstance()::getData();

if ($pages) {
    $pageController = new PageController();
    foreach ($pages as $page) {
        $page = (object)$page;
        Router::route($page->slug, function () use ($pageController, $page) {
            $pageController->render($page->name, $page);
        });
    }
}

# Posts
if ($posts = include(APP_DIR . '/data/posts.php')) {
    $postController = new PostController();
    foreach ($posts as $post) {
        $post = (object)$post;

        if (is_array($post->slug)) {

            $c = 0;
            foreach ($post->slug as $slug) {
                if ($c > 0) {
                    Router::addRedirect($slug, $post->slug[0]);
                }

                $lastChar = substr($slug, -1);
                if ($lastChar !== '/') {
                    //Router::addRedirect($slug, $slug . '/');
                }

                Router::route($slug, function () use ($postController, $post) {
                    $postController->render($post);
                });
                $c++;
            }
        } else {
            Router::route($post->slug, function () use ($postController, $post) {
                $postController->render($post);
            });
        }
    }
}


Router::route('/sitemap.xml', function () {
    include_once(APP_DIR . '/views/sitemap/sitemap_index.php');
});
Router::route('/sitemap/sitemap_categories', function () {
    include_once(APP_DIR . '/views/sitemap/sitemap_categories.php');
});
Router::route('/sitemap/sitemap_home', function () {
    include_once(APP_DIR . '/views/sitemap/sitemap_home.php');
});
Router::route('/sitemap/sitemap_pages', function () {
    include_once(APP_DIR . '/views/sitemap/sitemap_pages.php');
});
Router::route('/sitemap/sitemap_posts', function () {
    include_once(APP_DIR . '/views/sitemap/sitemap_posts.php');
});
Router::route('/sitemap/sitemap_tags', function () {
    include_once(APP_DIR . '/views/sitemap/sitemap_tags.php');
});
Router::route('/sitemap/sitemap_images', function () {
    // include_once(APP_DIR . '/views/sitemap/sitemap_images.php');
});


Router::execute($_SERVER['REQUEST_URI']);