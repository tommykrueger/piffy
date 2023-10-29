<?php

namespace App\Controllers;

use App\Collections\PostCollection;
use App\Framework\File;
use App\Framework\View;
use App\Helpers\DateHelper;
use App\Helpers\TextHelper;
use App\Models\BreadCrumb;
use App\Models\PostImage;
use stdClass;

class PostController
{
    private PostImage $postImage;

    public function __construct()
    {
        $this->postImage = new PostImage();
    }

    public function render(object $post): void
    {
        if (!is_object($post)) {
            View::render('404');
        }

        if (is_array($post->slug)) {
            $post->link = DOMAIN . $post->slug[0];
        } else {
            $post->link = DOMAIN . $post->slug;
        }

        $post->isArticle = true;
        $post->content = '';

        if ($post->image) {
            if (FALSE === strpos($post->image, 'http')) {
                // $post->image = $this->postImage->getImageSizeUrl($post->image, 1200, 676);
                // $post->image = $this->postImage->getResizedImageUrl($post->image, 1300, 676);

                $w = 1200;
                $h = 676;

                $targetFolder = APP_DIR . 'public/img/posts/' . $w . 'x' . $h;
                $targetFile = $targetFolder . '/' . $post->image;


                if (!is_dir($targetFolder)) {
                    @mkdir($targetFolder);
                }

                $this->postImage->resizeCropImage(
                    $w,
                    $h,
                    APP_DIR . 'public/img/posts/' . $post->image,
                    $targetFile
                );

                $post->image = $imagePath = DOMAIN . '/app/public/img/posts/' . $w . 'x' . $h . '/' . $post->image;

                //var_dump($post->image);
                //$post->image = DOMAIN . '/app/public/img/posts/' . $post->image;

            }
        }

        ob_start();
        View::post($post->id);
        $content = ob_get_clean();
        $post->words = TextHelper::getWordCount($content); // str_word_count(strip_tags($content));

        // approximately guess how long the post needs to read for a human
        $post->readingTime = ceil($post->words / 250) . ' Min';
        $post->created_format = date('d.m.Y', strtotime($post->created));
        $post->modified = File::getFileChangedDateTime(APP_DIR . '/views/posts/' . $post->file ?? $post->id . '.php', 'Y-m-d H:i:s');
        $post->modified_format = File::getFileChangedDateTime(APP_DIR . '/views/posts/' . $post->file ?? $post->id . '.php', 'd.m.Y');


        //if (isset($post->seo_title)) {
        //if (-1 !== strpos($post->seo_title, '{count}')) {
        //$post->seo_title = str_replace('{count}', count($entries), $post->seo_title);
        //}
        //}

        // get related posts
        $post->relatedPosts = [];

        $authors = include(APP_DIR . '/data/authors.php');
        $posts = include(APP_DIR . '/data/posts.php');
        $categories = include(APP_DIR . '/data/categories.php');

        $posts = array_reverse($posts);

        $catIDs = $post->categories;
        $tagIDs = $post->tags;
        $post->relatedPosts = array_filter($posts, function ($p) use ($catIDs, $tagIDs, $post) {
            $cats = $p['categories'];
            $tags = $p['tags'];
            return (!empty(array_intersect($cats, $catIDs)) || !empty(array_intersect($tags, $tagIDs))) && ($p['id'] !== $post->id) && (isset($p['teasable']) ? $p['teasable'] !== false : true);
        });

        $post->relatedPosts = array_slice($post->relatedPosts, 0, 8);

        for ($i = 0; $i < count($post->relatedPosts); $i++) {
            $post->relatedPosts[$i]['tags'] = null;
            $post->relatedPosts[$i]['excerpt'] = null;
            $post->relatedPosts[$i]['subtitle'] = null;

            if ($post->relatedPosts[$i]['image']) {
                $post->relatedPosts[$i]['image2x'] = $this->postImage->getImageSizeUrl($post->relatedPosts[$i]['image'], 1200, 676);
                $post->relatedPosts[$i]['image'] = DOMAIN . '/app/public/img/posts/' . $post->relatedPosts[$i]['image'];
            }

            if (!empty($post->relatedPosts[$i]['created'])) {
                $post->relatedPosts[$i]['date_full'] = DateHelper::getInstance()::getReadableDate($post->relatedPosts[$i]['created']);
            }

            $voteFile = APP_DIR . '/data/user-generated/votes/post_' . $post->relatedPosts[$i]['id'] . '.json';
            $voteFileData = @file_get_contents($voteFile);

            if ($voteFileData) {
                $post->relatedPosts[$i]['votes'] = json_decode($voteFileData);
            } else {
                $post->relatedPosts[$i]['votes'] = new stdClass();
                $post->relatedPosts[$i]['votes']->up = 0;
                $post->relatedPosts[$i]['votes']->down = 0;
            }

            if (isset($post->relatedPosts[$i]['authors'])) {
                $postAuthors = [];
                foreach ($post->relatedPosts[$i]['authors'] as $author) {
                    $t = (object)$author;
                    $postAuthors[] = $t;
                }
                $post->relatedPosts[$i]['authors'] = $postAuthors;
            }
        }

        if (isset($post->related_posts)) {
            $post->related_posts = array_values(array_filter($posts, function ($p) use ($post) {
                return (in_array($p['id'], $post->related_posts));
            }));

            $post->related_posts = array_reverse($post->related_posts);
        }

        if (isset($post->related_posts)) {
            for ($i = 0; $i < count($post->related_posts); $i++) {
                $post->related_posts[$i]['tags'] = null;
                $post->related_posts[$i]['excerpt'] = null;
                $post->related_posts[$i]['subtitle'] = null;

                if ($post->related_posts[$i]['image']) {
                    $post->related_posts[$i]['image2x'] = $this->postImage->getImageSizeUrl($post->related_posts[$i]['image'], 1200, 676);
                    $post->related_posts[$i]['image'] = DOMAIN . '/app/public/img/posts/' . $post->related_posts[$i]['image'];
                }


                if (!empty($post->related_posts[$i]['created'])) {
                    $post->related_posts[$i]['date_full'] = DateHelper::getInstance()::getReadableDate($post->related_posts[$i]['created']);
                }

                $dir = APP_DIR . 'data/user-generated/votes/';
                $file = $dir . 'post_' . $post->related_posts[$i]['id'] . '.json';

                if (file_exists($file)) {
                    $data = @file_get_contents($file);
                    $fileData = json_decode($data);
                    $post->related_posts[$i]['votes'] = $fileData->up ?? 0;
                } else {
                    $post->related_posts[$i]['votes'] = new stdClass();
                    $post->related_posts[$i]['votes']->up = 0;
                    $post->related_posts[$i]['votes']->down = 0;
                }

                $post->related_posts[$i]['link'] = DOMAIN . (is_array($post->related_posts[$i]['slug'])
                        ? $post->related_posts[$i]['slug'][0]
                        : $post->related_posts[$i]['slug']);
            }
        }


        $post->postsPageviews = PostCollection::getInstance()::getPostsOrderedByPageViews(5);

        $tags = include(APP_DIR . '/data/tags.php');
        if (!empty($post->tags)) {
            $postTags = [];
            foreach ($tags as $tag) {
                if (in_array($tag['id'], $post->tags)) {
                    $t = (object)$tag;
                    $t->link = DOMAIN . '/themen' . $t->slug;
                    $postTags[] = $t;
                }
            }
            $post->tags = $postTags;
        }


        if (isset($post->authors)) {

            /*
            $post->authorsList = array_values(array_filter($authors, function($author) use($post) {
                return (in_array($author['id'], $post->authors));
            }));
            */

            $postAuthors = [];
            foreach ($authors as $author) {
                if (in_array($author['id'], $post->authors)) {

                    $t = (object)$author;
                    $t->link = DOMAIN . '/autoren' . $t->slug;

                    $imgUrl = APP_DIR . '/public/img/personen/' . $t->image;
                    if (!empty($t->image) && file_exists($imgUrl)) {
                        $t->image = DOMAIN . '/app/public/img/personen/' . $t->image;
                    } else {
                        $t->image = DOMAIN . '/app/public/img/' . $t->gender . '.png';
                    }

                    $postAuthors[] = $t;
                }
            }
            $post->authors = $postAuthors;
        }

        $voteFile = APP_DIR . '/data/user-generated/votes/post_' . $post->id . '.json';
        $voteFileData = @file_get_contents($voteFile);

        if ($voteFileData) {
            $post->votes = json_decode($voteFileData);
        } else {
            $post->votes = new stdClass();
            $post->votes->up = 0;
            $post->votes->down = 0;
        }

        // load any comments of this post
        $commentsFile = APP_DIR . '/data/user-generated/comments/post_' . $post->id . '.json';
        $commentsFileData = @file_get_contents($commentsFile);

        if ($commentsFileData) {
            $post->comments = json_decode($commentsFileData);

            usort($post->comments->comments, function ($a, $b) {
                return ($a->created < $b->created);
            });
        }

        // take the first category as breadcrumb category
        if (isset($post->categories[0])) {
            $catID = $post->categories[0];
            $postCat = array_values(array_filter($categories, function ($cat) use ($catID) {
                return ($cat['id'] === $catID);
            }));
            $cat = (object)$postCat[0];
            BreadCrumb::addItem([
                'id' => $cat->id,
                'name' => $cat->title,
                'url' => DOMAIN . $cat->slug
            ]);
        }


        $file = APP_DIR . '/data/user-generated/page_votes.json';
        $fileData = @file_get_contents($file);
        $post->pageVote = json_decode($fileData);


        $file = APP_DIR . '/data/user-generated/list-likes/' . $post->id . '.json';
        if (file_exists($file)) {
            $fileData = @file_get_contents($file);
            $post->listLikes = json_decode($fileData);
        }

        BreadCrumb::addItem([
            'id' => $post->id,
            'name' => $post->title,
            'url' => $post->link
        ]);

        $post->breadcrumb = BreadCrumb::getItems();
        View::render('post', (object)$post);

        /*
        if (isset($post->layout) && 'large' === $post->layout) {
            render('post-large', (object)$post);
        } else {
            render('post', (object)$post);
        }
        */


    }

}