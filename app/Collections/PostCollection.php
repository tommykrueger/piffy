<?php

namespace App\Collections;

use App\Helpers\DateHelper;
use App\Models\PostImage;
use DateTime;
use stdClass;

/**
 * Class PostCollection
 *
 * Represents a collection of posts.
 */
class PostCollection
{

    private static $_instance = null;

    private static array $data;

    private static mixed $_authors;

    private static PostImage $postImage;

    /**
     * Class constructor
     *
     * This method is executed when an object of the class is created.
     * It initializes the class properties by setting the required values.
     *
     * @return void
     */
    private function __construct()
    {
        self::$data = self::getData();
        self::$_authors = include(APP_DIR . '/data/authors.php');
        self::$postImage = new PostImage();
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Retrieves random posts from the data source.
     *
     * @param int $size The number of random posts to retrieve. Default value is 10.
     *
     * @return array An array containing random posts.
     */
    public static function getRandomPosts(int $size = 10): array
    {
        // $posts = include(APP_DIR . '/data/posts.php');

        $filteredPosts = self::$data;
        shuffle($filteredPosts);

        return array_slice($filteredPosts, 0, $size);
    }

    /**
     * Retrieves data from the posts.php file, sorts and filters the data, and returns the final result.
     *
     * @return array An array containing the sorted and filtered posts data.
     */
    public static function getData()
    {
        $posts = include(APP_DIR . '/data/posts.php');
        $posts = array_reverse($posts);

        // order posts by date and merge with "repeatable" posts
        usort($posts, function ($a, $b) {
            //$a_year_day = getdate(strtotime($a["created"]))['yday'];
            //$b_year_day = getdate(strtotime($b["created"]))['yday'];
            //return $a_year_day < $b_year_day;

            return $a["created"] < $b["created"];
        });

        $d = time();
        $today = getdate();
        $now = new DateTime();
        $year = (int)date('Y');

        $posts = array_values(array_filter($posts, function ($post) use ($year) {
            $postYear = (int)getdate(strtotime($post["created"]))['year'];

            if ($year === $postYear) {
                return true;
            } elseif (isset($post['repeatable'])) {
                return true;
            }

            return false;
        }));


        // $posts = array_slice($posts, 0, 150);


        for ($i = 0; $i < count($posts); $i++) {
            $post = $posts[$i];
            $aDate = DateTime::createFromFormat('Y-m-d H:i:s', $post["created"]);

            $aMonth = $aDate->format('m');
            $aDay = $aDate->format('d');

            $aDateCurrentYear = DateTime::createFromFormat('Y-m-d', $year . '-' . $aMonth . '-' . $aDay);

            if ($aDateCurrentYear > $now) {
                $aDateCurrentYear = DateTime::createFromFormat('Y-m-d', ($year - 1) . '-' . $aMonth . '-' . $aDay);
            }

            $aDiff = $now->diff($aDateCurrentYear);
            //var_dump($aDiff->days);

            $post['normalized_date'] = $aDateCurrentYear->format('Y-m-d H:i:s');
            $post['normalized_date_diff'] = ($aDiff->days * ($aDiff->invert ? -1 : 1));

            //echo $post["created"] . ' ' . $post["normalized_date"] . ' --- ' . ($aDiff->days * ($aDiff->invert ? -1 : 1)) . ' <br/>';

            $posts[$i] = $post;
        }

        // var_dump($posts);

        usort($posts, function ($a, $b) use ($now, $year) {
            return $a["normalized_date_diff"] < $b["normalized_date_diff"];
        });


        /*
        $posts = array_values(array_filter($posts, function($post) use($d, $today) {
        
            if (isset($post['repeatable'])) {
                $postCreated = getdate(strtotime($post["created"]));
                return ($today['yday'] >= $postCreated['yday']);
            }
            else {
                return (
                    (($today['year'] === getdate(strtotime($post["created"]))['year']))
                    &&
                    $d > strtotime($post["created"]) && (isset($post['teasable']) ? $post['teasable'] !== false : true)
                );
            }
        
        }));
        */


        for ($i = 0; $i < count($posts); $i++) {
            if (!empty($posts[$i]['created'])) {
                $posts[$i]['date_full'] = DateHelper::getInstance()::getReadableDate($posts[$i]['created']);
            }
        }

        return $posts;
    }

    public static function getPublishedPosts()
    {
        $posts = self::getData();
        $posts = array_values(array_filter($posts, function ($post) {
            if (isset($post['published']) && $post['published'] === false) {
                return false;
            }
            return true;
        }));

        return $posts;
    }

    /**
     * @param int $id
     * @return bool|object
     */
    public static function getPost($id)
    {
        self::$data = include(APP_DIR . '/data/posts.php');
        $post = array_values(array_filter(self::$data, function ($post) use ($id) {
            return (int)$post['id'] === (int)$id;
        }));

        if (count($post) === 1) {
            $post = (object)$post[0];
            $post->url = is_array($post->slug) ? $post->slug[0] : $post->slug;
            $post->link = DOMAIN . (is_array($post->slug) ? $post->slug[0] : $post->slug);

            return $post;
        }
        return false;
    }

    public static function getPosts(array $ids): array
    {
        $posts = include(APP_DIR . '/data/posts.php');
        $posts = array_values(array_filter($posts, function ($post) use ($ids) {
            return in_array($post['id'], $ids);
        }));

        for ($i = 0; $i < count($posts); $i++) {

            if (!empty($posts[$i]['image'])) {
                $image = $posts[$i]['image'];
                // $posts[$i]['image_teaser'] = $postImage->getImageSizeUrl($posts[$i]['image'],280,158);
                // $posts[$i]['image_placeholder'] = $postImage->getImageSizeUrl($image,16,9);
                //$posts[$i]['image'] = $postImage->getImageSizeUrl($image,280,158);
                $posts[$i]['image'] = self::$postImage->getImageSizeUrl($image, 600, 338);
                $posts[$i]['image2x'] = self::$postImage->getImageSizeUrl($image, 1200, 676);
                //$posts[$i]['image'] = DOMAIN . '/app/public/img/posts/' . $posts[$i]['image'];
            }

            if (!empty($posts[$i]['authors'])) {
                $postAuthors = [];
                foreach (self::$_authors as $author) {
                    if (in_array($author['id'], $posts[$i]['authors'])) {

                        $t = (object)$author;
                        $t->link = DOMAIN . '/autoren' . $t->slug;

                        $imgUrl = APP_DIR . '/public/img/personen/' . $t->image;
                        if (file_exists($imgUrl)) {
                            $t->image = DOMAIN . '/app/public/img/personen/' . $t->image;
                        } else {
                            $t->image = DOMAIN . '/app/public/img/' . $t->gender . '.png';
                        }

                        $postAuthors[] = $t;
                    }
                }
                $posts[$i]['authors'] = $postAuthors;
            }

            if (!empty($posts[$i]['created'])) {
                $posts[$i]['date_full'] = DateHelper::getInstance()::getReadableDate($posts[$i]['created']);
            }

            $voteFile = APP_DIR . '/data/user-generated/votes/post_' . $posts[$i]['id'] . '.json';
            $voteFileData = @file_get_contents($voteFile);

            if ($voteFileData) {
                $posts[$i]['votes'] = json_decode($voteFileData);
            } else {
                $posts[$i]['votes'] = new stdClass();
                $posts[$i]['votes']->up = 0;
                $posts[$i]['votes']->down = 0;
            }


            $posts[$i]['link'] = DOMAIN . (is_array($posts[$i]['slug']) ? $posts[$i]['slug'][0] : $posts[$i]['slug']);
        }

        // $posts = array_slice($posts, 0, 3);

        $posts = self::sortByKeyList($posts, $ids);

        return $posts;
    }


    public static function getPostsByCategory(int $categoryId = 1, int $limit = 4): array
    {
        self::$postImage = new PostImage();

        if ($categoryId) {
            $posts = include(APP_DIR . '/data/posts.php');
            $posts = array_reverse($posts);
            $posts = array_values(array_filter($posts, function ($post) use ($categoryId) {
                return (in_array($categoryId, $post['categories']));
            }));

            $categories = include(APP_DIR . '/data/categories.php');
            $tags = include(APP_DIR . '/data/tags.php');

            for ($i = 0; $i < count($posts); $i++) {

                if (!empty($posts[$i]['image'])) {
                    if (FALSE === strpos($posts[$i]['image'], 'http')) {
                        $image = $posts[$i]['image'];
                        $posts[$i]['image'] = self::$postImage->getImageSizeUrl($image, 600, 338);
                        $posts[$i]['image2x'] = self::$postImage->getImageSizeUrl($image, 1200, 676);
                    }
                }

                if (!empty($posts[$i]['tags'])) {
                    $postTags = [];
                    foreach ($tags as $tag) {
                        if (in_array($tag['id'], $posts[$i]['tags'])) {
                            $t = (object)$tag;
                            $t->link = DOMAIN . '/themen' . $t->slug;
                            $postTags[] = $t;
                        }
                    }
                    $posts[$i]['tags'] = $postTags;
                }

                if (!empty($posts[$i]['categories'])) {
                    $postCategories = [];
                    foreach ($categories as $category) {
                        if (in_array($category['id'], $posts[$i]['categories'])) {
                            $t = (object)$category;
                            $t->link = $t->slug;
                            $postCategories[] = $t;
                        }
                    }
                    $posts[$i]['categories'] = $postCategories;
                }

                $voteFile = APP_DIR . '/data/user-generated/votes/post_' . $posts[$i]['id'] . '.json';
                $voteFileData = @file_get_contents($voteFile);

                if ($voteFileData) {
                    $posts[$i]['votes'] = json_decode($voteFileData);
                } else {
                    $posts[$i]['votes'] = new stdClass();
                    $posts[$i]['votes']->up = 0;
                    $posts[$i]['votes']->down = 0;
                }

                if (!empty($posts[$i]['created'])) {
                    $posts[$i]['date_full'] = DateHelper::getInstance()::getReadableDate($posts[$i]['created']);
                }

                $posts[$i] = (object)$posts[$i];
            }

            usort($posts, function ($a, $b) {
                return ($a->created < $b->created);
            });

            return array_slice($posts, 0, $limit);
        }

        return [];
    }


    public static function getPostsOrderedByPageViews($limit = 5)
    {
        $posts = self::$data;

        for ($i = 0; $i < count($posts); $i++) {
            $dir = APP_DIR . 'data/user-generated/pageviews/post/';
            $file = $dir . $posts[$i]['id'] . '.json';

            if (file_exists($file)) {
                $data = @file_get_contents($file);
                $fileData = json_decode($data);
                $posts[$i]['pageviews'] = $fileData->pageviews ?? 0;
            }
        }

        $posts = array_values(array_filter($posts, function ($post) {
            return isset($post['pageviews']);
        }));

        // order posts by date and merge with "repeatable" posts
        usort($posts, function ($a, $b) {
            return $a['pageviews'] < $b['pageviews'];
        });

        $posts = array_slice($posts, 0, $limit);

        for ($i = 0; $i < count($posts); $i++) {
            if (!empty($posts[$i]['image'])) {
                if (FALSE === strpos($posts[$i]['image'], 'http')) {
                    $image = $posts[$i]['image'];
                    $posts[$i]['image'] = self::$postImage->getImageSizeUrl($image, 120, 120);
                    $posts[$i]['image2x'] = self::$postImage->getImageSizeUrl($image, 240, 240);
                }
            }
        }

        return $posts;
    }


    public static function getPostsOrderedByVotes($limit = 5): array
    {
        $posts = self::$data;

        for ($i = 0; $i < count($posts); $i++) {
            $dir = APP_DIR . 'data/user-generated/votes/';
            $file = $dir . '/post_' . $posts[$i]['id'] . '.json';

            if (file_exists($file)) {
                $data = @file_get_contents($file);
                $fileData = json_decode($data);
                $posts[$i]['votes'] = $fileData->up ?? 0;
            }
        }

        $posts = array_values(array_filter($posts, function ($post) {
            return isset($post['votes']);
        }));

        // order posts by date and merge with "repeatable" posts
        usort($posts, function ($a, $b) {
            return $a['votes'] < $b['votes'];
        });

        $posts = array_slice($posts, 0, $limit);

        for ($i = 0; $i < count($posts); $i++) {
            if (!empty($posts[$i]['image'])) {
                if (FALSE === strpos($posts[$i]['image'], 'http')) {
                    $image = $posts[$i]['image'];
                    $posts[$i]['image'] = self::$postImage->getImageSizeUrl($image, 120, 120);
                    $posts[$i]['image2x'] = self::$postImage->getImageSizeUrl($image, 240, 240);
                }
            }
        }

        return $posts;
    }


    /**
     * @return array
     */
    public function getPostsOrderedByName(): array
    {
        $posts = include(APP_DIR . '/data/posts.php');
        usort($posts, function ($a, $b) {
            return $a['title'] > $b['title'];
        });
        return $posts;
    }


    private static function sortByKeyList(array $posts, array $keyList): array
    {
        $ret = [];
        if (empty($posts) || empty($keyList))
            return [];

        foreach ($keyList as $key) {
            foreach ($posts as $post) {
                if ($post['id'] === $key) {
                    $ret[] = $post;
                }
            }
        }
        return $ret;
    }
}