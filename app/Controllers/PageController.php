<?php

namespace App\Controllers;

use App\Collections\AuthorCollection;
use App\Collections\CategoryCollection;
use App\Collections\PostCollection;
use App\Framework\View;
use App\Models\BreadCrumb;
use App\Models\PostImage;

class PageController
{
    private PostImage $postImage;

    public function __construct()
    {
        $this->postImage = new PostImage();
    }

    public function render($name, $data)
    {

        $posts = include(APP_DIR . '/data/posts.php');
        //$tags = include(APP_DIR . '/data/tags.php');

        $data->link = DOMAIN . $data->slug;

        if ($name === 'suche') {
            if (isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
                $data->search = new stdClass();
                $data->search->query = $_REQUEST['s'];

                $query = strtolower($data->search->query);

                $filteredPosts = array_values(array_filter($posts, function ($post) use ($query) {
                    return (strpos(strtolower($post['title']), $query) !== false || (strpos(strtolower($post['excerpt']), $query)));
                }));

                for ($i = 0; $i < count($filteredPosts); $i++) {

                    /*
                    if (!empty($filteredPosts[$i]['tags'])) {
                        $postTags = [];
                        foreach ($tags as $tag) {
                            if (in_array($tag['id'], $filteredPosts[$i]['tags'])) {
                                $t = (object)$tag;
                                $t->link = DOMAIN . '/themen' . $t->slug;
                                $postTags[] = $t;
                            }
                        }
                        $filteredPosts[$i]['tags'] = $postTags;
                    }
                    */

                    if (!empty($filteredPosts[$i]['image'])) {
                        $filteredPosts[$i]['image'] = DOMAIN . '/app/public/img/posts/' . $filteredPosts[$i]['image'];
                    }
                }

                $data->posts = array_reverse($filteredPosts);
            } else {
                $data->content = 'Keine Daten vorhanden';
            }

            $data->title = 'Suche ... ';
        }


        if ($name === 'themen') {

            for ($i = 0; $i < count($tags); $i++) {

                $tagID = $tags[$i]['id'];
                $filteredPosts = array_filter($posts, function ($post) use ($tagID) {
                    return in_array($tagID, $post['tags']);
                });
                $tags[$i]['count'] = count($filteredPosts);

                $tags[$i]['link'] = DOMAIN . '/themen' . $tags[$i]['slug'];
            }
            $data->tags = $tags;
        }


        if ($name === 'homepage') {

            //$authors = AuthorCollection::getInstance()::getData();
            //$categories = CategoryCollection::getInstance()::getData();
            $posts = include(APP_DIR . '/data/posts.php');
            // $tags = include(APP_DIR . '/data/tags.php');

            //$data->authors = $authors;
            //$data->postCount = count($posts);

/*
            $categoriesCount = count($categories);
            for ($i = 0; $i < $categoriesCount; $i++) {
                $categories[$i]->posts = PostCollection::getInstance()::getPostsByCategory($categories[$i]->id, 100);
            }
            $data->categories = $categories;
*/
        }

        // add breadcrumb item for any page
        BreadCrumb::addItem([
            'id' => $data->id,
            'name' => $data->title,
        ]);
        $data->breadcrumb = BreadCrumb::getItems();


        $file = APP_DIR . '/data/user-generated/page_votes.json';
        $fileData = @file_get_contents($file);
        $data->pageVote = json_decode($fileData);


        if (isset($data->template)) {
            View::render($data->template, $data);
            exit;
        } else {
            View::render('page', $data);
        }

    }

}