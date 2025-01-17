<?php

namespace Piffy\Controllers;

use App\Models\BreadCrumb;
use App\Models\PostImage;
use Exception;
use Piffy\Collections\PostCollection;
use Piffy\Enum\Events;
use Piffy\Framework\Cache;
use Piffy\Framework\Log;
use Piffy\Framework\View;
use Piffy\Models\Post;
use Piffy\Plugins\Newsletter\Models\Email;
use stdClass;

/**
 * Class PostController
 *
 * The PostController class is responsible for rendering a single post.
 */
class PostController
{
    public array $response = array(
        'message' => '',
        'status' => true
    );

    private ?PostImage $postImage = null;
    
    public function __construct()
    {
        $this->postImage = new PostImage();
    }
    
    public function render(Post $post): void
    {
        Cache::start($post->getFileName(), $post->cache);

        if (!is_object($post)) {
            View::render('404');
        }

        $post->prepareData();

        //dump($post);
        //exit;

        if (is_array($post->slug)) {
            $post->link = DOMAIN . $post->slug[0];
        } else {
            $post->link = DOMAIN . $post->slug;
        }


        $post->isArticle = true;
        $post->content = '';
      
        if (!empty($post->image)) {
            if (!str_contains($post->image, 'http')) {
                // $post->image = $this->postImage->getImageSizeUrl($post->image, 1200, 676);
                // $post->image = $this->postImage->getResizedImageUrl($post->image, 1300, 676);

                $w = 1200;
                $h = 676;

                $targetFolder = BASE_DIR . '/public/img/posts/'. $w . 'x' . $h;
                $targetFile = $targetFolder . '/' . $post->image;


                if (!is_dir($targetFolder)) {
                    mkdir($targetFolder);
                }

                $this->postImage->resizeCropImage(
                    $w,
                    $h,
                    BASE_DIR . '/public/img/posts/' . $post->image,
                    $targetFile
                );

                $post->image = DOMAIN . '/public/img/posts/'. $w . 'x' . $h . '/' . $post->image;

                // var_dump($post->image);
                //$post->image = DOMAIN . '/app/public/img/posts/' . $post->image;

            }
        }

        // var_dump($post->image);

        //ob_start();
        //View::post($post->getId());
        //$content = ob_get_clean();
        // $post->words = TextHelper::getWordCount($content); // str_word_count(strip_tags($content));
        // $post->body = $content;


        // $post->words = str_word_count(strip_tags($content));
        
        // approximately guess how long the post needs to read for a human
        //$post->readingTime = ceil($post->words / 250) . ' Min';
        //$post->created_format = date('d.m.Y', strtotime($post->created));
        //$post->modified = File::getFileChangedDateTime(APP_DIR . '/views/posts/' . $post->getFileName() . '.php', 'Y-m-d H:i:s');
        // $post->modified_format = File::getFileChangedDateTime(APP_DIR . '/views/posts/' . $post->getFileName() . '.php', 'd.m.Y');
        
        if (isset($post->data_provider)) {
            $data = include DATA_DIR . '' . $post->data_provider;
            $post->title = $post->seo_title = str_replace('{count}', count($data), $post->seo_title);
        }
        
        // get related posts
        $post->relatedPosts = [];
        
        $authors = include(DATA_DIR . 'authors.php');
        $posts = include(DATA_DIR . 'posts.php');
        $categories = include(DATA_DIR . 'categories.php');
    
        $posts = array_reverse($posts);

        $post->relatedPosts = PostCollection::getInstance()->getRelatedPosts($post);


        $catIDs = $post->categories;
        $tagIDs = $post->tags;
        /*
        $post->relatedPosts = array_filter($posts, function($p) use($catIDs, $tagIDs, $post) {
            $cats = $p['categories'];
            $tags = $p['tags'];
            return (!empty(array_intersect($cats, $catIDs)) || !empty(array_intersect($tags, $tagIDs))) && ($p['id'] !== $post->id) && (isset($p['teasable']) ? $p['teasable'] !== false : true);
        });
        */

        $post->relatedPosts = array_slice($post->relatedPosts,0, 8);

        /*
        for ($i=0; $i<count($post->relatedPosts); $i++) {
            //$post->relatedPosts[$i]['tags'] = null;
            //$post->relatedPosts[$i]['excerpt'] = null;
            //$post->relatedPosts[$i]['subtitle'] = null;
            
            if ($post->relatedPosts[$i]['image']) {
                $post->relatedPosts[$i]['image2x'] = $this->postImage->getImageSizeUrl($post->relatedPosts[$i]['image'],1200,676);
                $post->relatedPosts[$i]['image'] = DOMAIN . '/app/public/img/posts/' . $post->relatedPosts[$i]['image'];
            }
    
            if (!empty($post->relatedPosts[$i]['created'])) {
                $post->relatedPosts[$i]['date_full'] = DateHelper::getInstance()::getReadableDate($post->relatedPosts[$i]['created']);
            }

            $voteFile = USERDATA_DIR . '/votes/post_' . $post->relatedPosts[$i]['id'].  '.json';
            $voteFileData = @file_get_contents($voteFile);

            if ($voteFileData) {
                $post->relatedPosts[$i]['votes'] = json_decode($voteFileData);
            } else{
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
        */


    
        if (isset($post->related_posts)) {
            $post->related_posts =  PostCollection::getInstance()->getPosts($post->related_posts);
        }

        // $post->postsPageviews = PostCollection::getInstance()->getPostsOrderedByPageViews(5);
    
        $tags = include(DATA_DIR . 'tags.php');
        if ( !empty($post->tags)) {
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
        
        $voteFile = USERDATA_DIR . '/votes/post_' .  $post->getId().  '.json';
        $voteFileData = @file_get_contents($voteFile);
    
        if ($voteFileData) {
            $post->votes = json_decode($voteFileData);
        } else{
            $post->votes = new stdClass();
            $post->votes->up = 0;
            $post->votes->down = 0;
        }

        // take the first category as breadcrumb category
        if (isset($post->categories[0])) {
            $catID = $post->categories[0];
            $postCat = array_values(array_filter($categories, function($cat) use($catID) {
                return ($cat['id'] === $catID);
            }));
            $cat = (object)$postCat[0];
            BreadCrumb::addItem([
                'id' => $cat->id,
                'name' => $cat->title,
                'url' => DOMAIN . $cat->slug
            ]);
        }
    
    
        $file = USERDATA_DIR . '/page_votes.json';
        $fileData = @file_get_contents($file);
        $post->pageVote = json_decode($fileData);


        $file = USERDATA_DIR . '/list-likes/' . $post->getId() .  '.json';
        if (file_exists($file)) {
            $fileData = @file_get_contents($file);
            $post->listLikes = json_decode($fileData);
        }
    
        BreadCrumb::addItem([
            'id' => $post->getId(),
            'name' => $post->getTitle(),
            'url' => $post->getUrl()
        ]);

        $post->breadcrumb = BreadCrumb::getItems();
        View::render($post->template ?? 'post', $post);

        Cache::end();
        
        /*
        if (isset($post->layout) && 'large' === $post->layout) {
            View::render('post-large', (object)$post);
        } else {
            View::render('post', (object)$post);
        }
        */
        
       
    }


    /**
     * Add a like to a certain list entry of a post
     * @param $id
     * @return void
     */
    public function addListLike($id): void
    {
        $data = $_REQUEST['data'];
        $data = json_decode(html_entity_decode(stripslashes($data)), false);

        @session_start();
        $IP = $_SERVER['REMOTE_ADDR'];

        // save the vote to local file
        $file = USERDATA_DIR . '/list-likes/' . $id . '.json';
        $fileData = @file_get_contents($file);

        if (!$fileData) {
            $fileDataObject = new stdClass();
        } else {
            $fileDataObject = json_decode($fileData);
        }

        $val = 1;
        if (isset($fileDataObject->{$data->name})) {
            $val = (int)$fileDataObject->{$data->name} + 1;
        }
        $fileDataObject->{$data->name} = $val;

        @file_put_contents($file, json_encode($fileDataObject));
        Cache::clear($_SERVER['HTTP_REFERER']);

        try {
            $post = PostCollection::getInstance()->getPost($id);
            $emailData['id'] = $id;
            $emailData['post'] = $post;
            $emailData['likes'] = $val;
            $emailData['name'] = $data->name;
            $email = new Email((object)[
                'recipient' => 'info@lachvegas.de',
                'emailData' => (object)$emailData,
                'subject' => 'New Like #' . date('Y-m-d'),
                'emailTemplate' => 'post_list_like',
            ]);
            $email->send();
        } catch (Exception $e) {

        }

        $this->response['status'] = true;
        $this->respond();
    }

    public function postVote($id): void
    {
        $data = $_REQUEST['data'];
        $d = json_decode(html_entity_decode(stripslashes($data)), false);

        @session_start();
        $IP = $_SERVER['REMOTE_ADDR'];

        // save the vote to local file
        $voteFile = USERDATA_DIR . '/votes/post_' . $id . '.json';
        $voteFileData = @file_get_contents($voteFile);

        if ($voteFileData) {
            $votes = json_decode($voteFileData);
        }

        if (!isset($votes)) {
            $votes = new stdClass();
            $votes->up = 0;
            $votes->down = 0;
        }

        if (isset($d->vote_up)) {
            $postVotesUp = $votes->up;

            if (!is_numeric($postVotesUp)) {
                $postVotesUp = 0;
            }

            $postVotesUp++;
            $votes->up = $postVotesUp;
            $this->response['vote_up'] = true;
            $this->response['votes'] = $postVotesUp;
        } elseif (isset($d->vote_down)) {
            $postVotesDown = $votes->down;

            if (!is_numeric($postVotesDown)) {
                $postVotesDown = 0;
            }

            $postVotesDown++;
            $votes->down = $postVotesDown;

            $this->response['vote_down'] = true;
            $this->response['votes'] = $postVotesDown;
        }


        @file_put_contents($voteFile, json_encode($votes));

        $post = PostCollection::getInstance()->getPost((int)$id);

        try {
            $emailData['id'] = $id;
            $emailData['post'] = $post;
            $emailData['likes'] = $votes->up;
            $email = new Email((object)[
                'recipient' => SYSTEM_EMAIL,
                'emailData' => (object)$emailData,
                'subject' => 'New Post Like #' . date('Y-m-d'),
                'emailTemplate' => 'post_like',
            ]);
            $email->send();
        } catch (Exception $e) {
            Log::warning($e->getMessage());
        }

        // EventService::trigger(EventManager::POST_VOTED, $post);
        event(Events::POST_VOTED, $post);

        $this->response['status'] = true;
        $this->respond();
    }

    public function savePageView(int $id): void
    {
        $requestData = $_REQUEST['data'];
        $requestData = json_decode(html_entity_decode(stripslashes($requestData)), false);

        // save the vote to local file
        $dataFile = USERDATA_DIR . '/pageviews/post/' . $id . '.json';
        $dataFileContent = @file_get_contents($dataFile);

        if ($dataFileContent) {
            $data = json_decode($dataFileContent);
        }
        if (!isset($data)) {
            $data = new stdClass();
            $data->pageviews = 0;
        }

        if (isset($requestData->type)) {
            $data->pageviews++;
        }

        @file_put_contents($dataFile, json_encode($data));
        Cache::clear($_SERVER['HTTP_REFERER']);

        /*
        $views = 0;

        $db = new DB_PDO();

        try {
            $query = 'SELECT * from post_views WHERE post_id = '. $id;
            $statement = $db->prepare($query);

            if (!$statement) {
                echo $statement->errorCode();
            }

            $statement->execute();

            $data = $statement->fetchAll();

            if (!empty($data)) {
                $views = $data[0]['views'];
                $views++;
            }

            $statement->closeCursor();
        } catch (\Throwable $e) {
            echo $query;
            echo $e->getMessage();
        }

        try {

            if (0 === $views) {
                $query = 'INSERT INTO post_views (post_id, views, created) VALUES ('. $id .','. $views .', now())';
            } else {
                $query = 'UPDATE post_views SET views = '. $views . ', updated = now() WHERE post_id = ' . $id;
            }


            $statement = $db->prepare($query);

            if (!$statement) {
                echo $statement->errorCode();
            }

            $statement->execute();
            $statement->closeCursor();
        } catch (\Throwable $e) {
            echo $query;
            echo $e->getMessage();
        }
        */

        $this->response['status'] = true;
        $this->respond();
    }


    private function respond(): void
    {
        echo json_encode($this->response);
        exit;
    }

}