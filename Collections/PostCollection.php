<?php

namespace Piffy\Collections;

use App\Models\PostImage;
use DateTime;
use Piffy\Framework\Collection;
use Piffy\Helpers\DateHelper;
use Piffy\Models\Post;

class PostCollection extends Collection
{
    protected static $data = null;

    private static PostImage $postImage;
    public string $model = Post::class;
    public string $source = DATA_DIR . 'posts.php';

    public function __construct()
    {
        parent::__construct();
        self::$postImage = new PostImage();
    }

    public function getRandomPosts($size = 10): array
    {
        $posts = $this->getAll();
        shuffle($posts);
        return array_slice($posts, 0, $size);
    }

    /**
     * @return mixed|null
     */
    public static function getData()
    {
        $posts = include(DATA_DIR . 'posts.php');
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

    public static function getPublishedPosts(): array
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
     * @return Post
     */
    public function getPost(int $id): ?Post
    {
        $post = array_values(array_filter($this->getAll(), function ($post) use ($id) {
            return $post->id === $id;
        }));
        //if (count($post) === 1) {
            $post = $post[0];
            $post->url = is_array($post->slug) ? $post->slug[0] : $post->slug;
            $post->link = DOMAIN . (is_array($post->slug) ? $post->slug[0] : $post->slug);

            return $post;
        //}
        return null;
    }

    public function getPosts(array $ids): array
    {
        $posts = array_values(array_filter($this->getAll(), function ($post) use ($ids) {
            return in_array($post->getId(), $ids);
        }));

        /*
        for ($i=0; $i<count($posts); $i++) {
    
            if (!empty($posts[$i]->image)) {
                $image = $posts[$i]->image;
                // $posts[$i]['image_teaser'] = $postImage->getImageSizeUrl($posts[$i]['image'],280,158);
                // $posts[$i]['image_placeholder'] = $postImage->getImageSizeUrl($image,16,9);
                //$posts[$i]['image'] = $postImage->getImageSizeUrl($image,280,158);
                $posts[$i]->image = self::$postImage->getImageSizeUrl($image, 600, 338);
                $posts[$i]->image2x = self::$postImage->getImageSizeUrl($image, 1200, 676);
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
    
            if (!empty($posts[$i]->created)) {
                $posts[$i]->date_full = DateHelper::getInstance()::getReadableDate($posts[$i]->created);
            }

            /*
            $voteFile = USERDATA_DIR . '/votes/post_' . $posts[$i]['id'].  '.json';
            $voteFileData = @file_get_contents($voteFile);

            if ($voteFileData) {
                $posts[$i]['votes'] = json_decode($voteFileData);
            } else{
                $posts[$i]['votes'] = new stdClass();
                $posts[$i]['votes']->up = 0;
                $posts[$i]['votes']->down = 0;
            }

            $posts[$i]->link = DOMAIN . (is_array($posts[$i]['slug']) ? $posts[$i]['slug'][0] : $posts[$i]['slug']);
        }
        */

        // $posts = array_slice($posts, 0, 3);

        $posts = self::sortByKeyList($posts, $ids);

        return $posts;
    }


    public function getPostsByCategory($categoryId = 1, $limit = 4): array
    {
        if ($categoryId) {
            $posts = array_reverse($this->_data);
            $posts = array_values(array_filter($posts, function ($post) use ($categoryId) {
                return (in_array($categoryId, $post->categories));
            }));

            usort($posts, function ($a, $b) {
                return ($a->created < $b->created);
            });

            return array_slice($posts, 0, $limit);
        }
        return [];
    }

    public function getPostsByTags($tagId = 1, $limit = 5): array
    {
        if ($tagId) {
            $posts = array_reverse($this->_data);
            $posts = array_values(array_filter($posts, function ($post) use ($tagId) {
                return (in_array($tagId, $post->tags));
            }));

            usort($posts, function ($a, $b) {
                return ($a->created < $b->created);
            });

            return array_slice($posts, 0, $limit);
        }
        return [];
    }

    public function getPostsOrderedByPageViews($limit = 5): array
    {
        $posts = $this->getAll();

        for ($i = 0; $i < count($posts); $i++) {
            $dir = USERDATA_DIR . '/pageviews/post/';
            $file = $dir . $posts[$i]->id . '.json';

            if (file_exists($file)) {
                $data = @file_get_contents($file);
                $fileData = json_decode($data);
                $posts[$i]->pageviews = $fileData->pageviews ?? 0;
            }
        }

        $posts = array_values(array_filter($posts, function ($post) {
            return isset($post->pageviews);
        }));

        // order posts by date and merge with "repeatable" posts
        usort($posts, function ($a, $b) {
            return (int)($a->pageviews < $b->pageviews);
        });

        $posts = array_slice($posts, 0, $limit);

        /*
        for ($i=0; $i<count($posts); $i++) {
            if (!empty($posts[$i]['image'])) {
                if (FALSE === strpos($posts[$i]['image'], 'http')) {
                    $image = $posts[$i]['image'];
                    $posts[$i]['image'] = self::$postImage->getImageSizeUrl($image, 120, 120);
                    $posts[$i]['image2x'] = self::$postImage->getImageSizeUrl($image, 240, 240);
                }
            }
        }
        */

        return $posts;
    }


    public function getPostsOrderedByVotes($limit = 5): array
    {
        // $posts = include(DATA_DIR . 'posts.php');

        $posts = $this->getAll();
        for ($i = 0; $i < count($posts); $i++) {
            $dir = USERDATA_DIR . '/votes/';
            $file = $dir . '/post_' . $posts[$i]->id . '.json';

            if (file_exists($file)) {
                $data = @file_get_contents($file);
                $fileData = json_decode($data);
                $posts[$i]->votes = $fileData->up ?? 0;
            }
        }

        $posts = array_values(array_filter($this->getAll(), function ($post) {
            return isset($post->votes);
        }));

        // order posts by date and merge with "repeatable" posts
        usort($posts, function ($a, $b) {
            return $a->votes < $b->votes;
        });

        return array_slice($posts, 0, $limit);
    }


    public function getPostsOrderedByDate(int $limit = 5, array $exclude = []): array
    {
        $posts = array_values(array_filter($this->getAll(), function ($post) use ($exclude) {
            return !in_array($post->id, $exclude);
        }));

        // order posts by date and merge with "repeatable" posts
        usort($posts, function ($a, $b) {
            return $a->created < $b->created;
        });

        return array_slice($posts, 0, $limit);
    }


    /**
     * @return array
     */
    public function getPostsOrderedByName(): array
    {
        $posts = $this->getAll();
        usort($posts, function ($a, $b) {
            return $a->getTitle() > $b->getTitle();
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
                if ($post->id === $key) {
                    $ret[] = $post;
                }
            }
        }
        return $ret;
    }

    public function getByTagId($id): array
    {
        $data = array_values(array_filter($this->_data, function ($d) use ($id) {
            return in_array($id, $d->tags);
        }));

        usort($data, function ($a, $b) {
            return (int)($a->created < $b->created);
        });

        return $data;
    }


    public function getRelatedPosts($post): array
    {
        $catIDs = $post->categories;
        $tagIDs = $post->tags;
        $relatedPosts = array_filter($this->_data, function ($p) use ($catIDs, $tagIDs, $post) {
            $cats = $p->categories;
            $tags = $p->tags;
            return (!empty(array_intersect($cats, $catIDs)) || !empty(array_intersect($tags, $tagIDs))) && ($p->id !== $post->id) && (isset($p->teasable) ? $p->teasable !== false : true);
        });

        return $relatedPosts;
    }

    /**
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getPostsOrderedByDateOverflow(int $limit = 12): array
    {
        $posts = $this->getAll();
        $postCount = count($posts);
        $today = getdate(strtotime('+1 day'));
        // $today = getdate(strtotime('2024-03-01'));

        $currentYear = date('Y');
        $lastYear = $currentYear - 1;

        for ($i = 0; $i < $postCount; $i++) {
            $dateObj = getdate(strtotime($posts[$i]->created));
            // $dateObj = getdate(strtotime($posts[$i]->modified));

            if (!empty($dateObj)) {
                // var_dump($dateObj);

                $day = $dateObj['mday'];
                $month = $dateObj['mon'];
                $dayOfTheYear = $dateObj['yday'];

                // use last year for comparison
                if ($dayOfTheYear >= $today['yday']) {
                    $dayDiff = DateHelper::getInstance()->getDaysBetweenDates("$day-$month-$lastYear", date('d-m-Y'));
                } else {
                    $dayDiff = DateHelper::getInstance()->getDaysBetweenDates("$day-$month-$currentYear", date('d-m-Y'));
                }

                $posts[$i]->diff = $dayDiff;
            }
        }

        usort($posts, function ($a, $b) use ($today) {
            return $a->diff >= $b->diff;
        });


        return $posts;
    }


    public function getRecommendedPosts($limit = 12): array
    {
        $posts = $this->getAll();

        $filteredPosts = array_values(array_filter($posts, function ($post) {
            if (isset($post->recommended) && $post->recommended === true) {
                return true;
            }
            return false;
        }));

        shuffle($filteredPosts);

        return array_splice($filteredPosts, 0, $limit);
    }
}