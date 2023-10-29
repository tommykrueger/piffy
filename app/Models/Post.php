<?php

// include(APP_DIR . '/models/email.php');
namespace App\Models;

class Post
{

    protected $_id;

    protected $toBreadCrumbAdded = false;

    protected $response = array(
        'message' => '',
        'status' => true
    );

    public function __construct()
    {

    }

    public function render($id)
    {

        // todo load the data
        if ($id) {
            $post = new stdClass();

            include(APP_DIR . '/data/posts.php');
            include(APP_DIR . '/data/categories.php');

            $data = array_filter($posts, function ($post) use ($id) {
                return $id == $post['id'];
            });

            if (!count($data)) {
                header("HTTP/1.1 404 Not Found");
                render('404', [
                    'title' => '404 - Nüscht gefunden'
                ]);
                exit;
            }

            foreach ($data as $d) {
                $post = (object)$d;
            }

            $sidebarMenu = [];
            for ($i = 0; $i < count($categories); $i++) {

                if (in_array($categories[$i]['id'], $post->categories)) {
                    BreadCrumb::addItem([
                        'id' => $categories[$i]['id'],
                        'name' => $categories[$i]['name'],
                        'url' => Category::getUrl($categories[$i]['id'], true),
                    ]);
                }

                if (isset($categories[$i]['children'])) {
                    for ($j = 0; $j < count($categories[$i]['children']); $j++) {
                        $id = $categories[$i]['children'][$j]['id'];
                        $filteredPosts = array_filter($posts, function ($p) use ($id, $post) {
                            return in_array($id, $post->categories);
                        });

                        if (in_array($id, $post->categories) && !$this->toBreadCrumbAdded) {
                            BreadCrumb::addItem([
                                'id' => $categories[$i]['id'],
                                'name' => $categories[$i]['name'],
                                'url' => Category::getUrl($categories[$i]['id'], true),
                            ]);
                            BreadCrumb::addItem([
                                'id' => $categories[$i]['children'][$j]['id'],
                                'name' => $categories[$i]['children'][$j]['name'],
                                'url' => Category::getUrl($categories[$i]['children'][$j]['id'], true),
                            ]);
                            $this->toBreadCrumbAdded = true;
                        }

                        $categories[$i]['children'][$j]['count'] = count($filteredPosts);
                        $sidebarMenu[] = $categories[$i]['children'][$j];
                    }
                }

            }

            usort($sidebarMenu, function ($a, $b) {
                return $a['name'] > $b['name'];
            });

            $post->menu = (object)$categories;
            $post->sidebarMenu = $sidebarMenu;
            $post->type = 'post';

            $voteFile = APP_DIR . '/data/user-generated/votes/post_' . $id . '.json';
            $voteFileData = @file_get_contents($voteFile);

            if ($voteFileData) {
                $post->votes = json_decode($voteFileData);
            } else {
                $post->votes = new stdClass();
                $post->votes->up = 0;
                $post->votes->down = 0;
            }

            if (count($data) === 1) {
                BreadCrumb::addItem([
                    'id' => $post->id,
                    'name' => 'Witz #' . $post->id,
                    'url' => DOMAIN . '/witz/' . $post->id
                ]);
                $post->breadcrumb = BreadCrumb::getItems();
                render('post', $post);
            }
        } else {
            header("HTTP/1.1 404 Not Found");
            render('404');
        }
    }


    public function generateImage($data = null, $isAjax = true)
    {
        //$params = isset($_REQUEST['params']) ? $_REQUEST['params'] : [];

        if (isset($_REQUEST['id']) && isset($_REQUEST['title'])) {
            $data = new stdClass();
            $data->id = $_REQUEST['id'];
            $data->title = $_REQUEST['title'];
            $data->content = $_REQUEST['content'];
            $data->categories = $_REQUEST['categories'];
        }

        if (!isset($data->title) || empty($data->title)) {
            $this->response = [
                'message' => 'not saved - no title',
                'status' => true
            ];
            $this->respond();
        }

        $this->createPostImage($data);

        if ($isAjax) {
            $this->response = [
                'message' => 'saved',
                'status' => true
            ];

            $this->respond();
        }

        return true;
    }


    private function createPostImage($post)
    {

        //$postImage = false;
        // $path = get_template_directory() . '/app/images/' . sanitize_title($post->post_title) . '_640x640.png';
        //$url = false;

        //if (file_exists($path)) {
        //$url = get_bloginfo('template_url') . '/app/images/' . sanitize_title($post->post_title) . '_640x640.png';
        //} else {

        include_once APP_DIR . '/lib/classes/image.php';

        $options = new stdClass();
        $options->text = !empty($post->content) ? $post->content : $post->title;
        $options->slug = $post->id;

        if (isset($post->categories[0])) {
            $slug = CategoryCollection::getCategorySlugById($post->categories[0]);
            if (file_exists(APP_DIR . 'public/img/categories/' . $slug . '.jpg')) {
                $options->bgImage = APP_DIR . 'public/img/categories/' . $slug . '.jpg';
            }
        }
        $image = new Image($options);
        $createdImageInUploadsDir = $image->generate();

        //$options->width = 1200;
        //$options->height = 675;
        //$image = new Image($options);
        //$createdImageInUploadsDir = $image->generate();

        //update_post_meta($post->ID, 'import_image_url', $url);
        // $image->savePostThumbnail($post->ID, $createdImageInUploadsDir);

        //}

        //return $url;

    }


    public function savePageView($id)
    {
        $requestData = $_REQUEST['data'];
        $requestData = json_decode(html_entity_decode(stripslashes($requestData)), false);

        // save the vote to local file
        $dataFile = APP_DIR . '/data/user-generated/pageviews/post/' . $id . '.json';
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

        $this->response['status'] = true;
        $this->respond();
    }


    public function postVote($id)
    {
        $data = $_REQUEST['data'];
        $d = json_decode(html_entity_decode(stripslashes($data)), false);

        @session_start();
        $IP = $_SERVER['REMOTE_ADDR'];

        // save the vote to local file
        $voteFile = APP_DIR . '/data/user-generated/votes/post_' . $id . '.json';
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
        } else if (isset($d->vote_down)) {
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


        try {
            $post = PostCollection::getInstance()::getPost($id);
            $emailData['id'] = $id;
            $emailData['post'] = $post;
            $emailData['likes'] = $votes->up;
            $email = new Email((object)[
                'recipient' => 'info@lachvegas.de',
                'emailData' => (object)$emailData,
                'subject' => 'New Post Like #' . date('Y-m-d'),
                'emailTemplate' => 'post_like',
            ]);
            $email->send();
        } catch (Exception $e) {

        }


        Cache::clear($_SERVER['HTTP_REFERER']);

        $this->response['status'] = true;
        $this->respond();
    }


    /**
     * Add a like to a certain list entry of a post
     * @param $id
     * @return void
     */
    public function addListLike($id)
    {
        $data = $_REQUEST['data'];
        $data = json_decode(html_entity_decode(stripslashes($data)), false);

        @session_start();
        $IP = $_SERVER['REMOTE_ADDR'];

        // save the vote to local file
        $file = APP_DIR . '/data/user-generated/list-likes/' . $id . '.json';
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
            $post = PostCollection::getInstance()::getPost($id);
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

    /**
     * Adds a like to a certain entry of a post list
     * @param $id
     * @todo implement this
     */
    public function postEntryLike($id)
    {
        $data = $_REQUEST['data'];
        $d = json_decode(html_entity_decode(stripslashes($data)), false);

        @session_start();
        $IP = $_SERVER['REMOTE_ADDR'];

        // save the vote to local file

        Cache::clear($_SERVER['HTTP_REFERER']);

        $this->response['status'] = true;
        $this->respond();
    }


    public function addComment($id)
    {

        @session_start();
        $IP = $_SERVER['REMOTE_ADDR'];
        $_SESSION['session_ip'] = $IP;

        if ($id) {
            $data = $_REQUEST['data'];
            $request = (object)json_decode($data);


            // save the vote to local file
            $file = APP_DIR . '/data/user-generated/comments/post_' . $id . '.json';
            $fileData = @file_get_contents($file);

            if ($fileData) {
                $comments = json_decode($fileData);
            }

            if (!isset($comments)) {
                $comments = new stdClass();
                $comments->id = (int)$id;
                $comments->comments = [];
            }


            if ($request->action === 'post_comment_like') {
                if (!isset($request->like)) {
                    $this->response['status'] = false;
                    $this->respond();
                }

                for ($i = 0; $i < count($comments->comments); $i++) {
                    if ($comments->comments[$i]->id === (int)$request->id) {
                        $comments->comments[$i]->likes++;
                        $this->response['likes'] = $comments->comments[$i]->likes;
                    }
                }
            }
            if ($request->action === 'post_comment_dislike') {
                if (!isset($request->dislike)) {
                    $this->response['status'] = false;
                    $this->respond();
                }

                for ($i = 0; $i < count($comments->comments); $i++) {
                    if ($comments->comments[$i]->id === (int)$request->id) {
                        $comments->comments[$i]->dislikes++;
                        $this->response['dislikes'] = $comments->comments[$i]->dislikes;
                    }
                }
            }

            if ($request->action === 'post_comment') {
                if (strlen($request->author) <= 1) {
                    $this->response['status'] = false;
                    $this->respond();
                }
                if (strlen($request->text) <= 1) {
                    $this->response['status'] = false;
                    $this->respond();
                }

                $request->author = html_entity_decode(stripslashes($request->author));
                $request->text = html_entity_decode(stripslashes($request->text));
                $request->parent = $request->parent ?? 0;

                $comments->comments[] = [
                    'id' => count($comments->comments) + 1,
                    'parent' => $request->parent ?? 0,
                    'created' => date('Y-m-d H:i:s'),
                    'text' => $request->text,
                    'author' => $request->author,
                    'likes' => 0,
                    'dislikes' => 0,
                    'status' => 'publish'
                ];

            }


            @file_put_contents($file, json_encode($comments));

            Cache::clear($_SERVER['HTTP_REFERER']);

            try {

                $posts = include(APP_DIR . '/data/posts.php');
                $post = array_values(array_filter($posts, function ($post) use ($id) {
                    return ($post['id'] === (int)$id);
                }));

                if ($post) {
                    $post = (object)$post[0];

                    $email = new Email((object)[
                        'recipient' => 'info@lachvegas.de',
                        'subject' => 'Neuer Kommentar auf lachvegas.de',
                        'emailTemplate' => 'new_comment',
                        'emailData' => [
                            'postID' => $id,
                            'postTitle' => $post->title,
                            'postUrl' => DOMAIN . $post->slug,
                            'comment' => $request->text,
                        ]
                    ]);
                    $email->send();

                }


            } catch (Exception $e) {

            }

            $this->response['status'] = true;
            $this->respond();
        }
    }


    private function respond()
    {
        echo json_encode($this->response);
        exit;
    }

}