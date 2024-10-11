<?php

namespace Piffy\Models;

use App\Collections\CategoryCollection;
use App\Collections\PostCollection;
use App\Models\PostImage;
use Exception;
use Piffy\Framework\Cache;
use Piffy\Framework\File;
use Piffy\Framework\Model;
use Piffy\Framework\View;
use Piffy\Helpers\TextHelper;
use Piffy\Plugins\Newsletter\Models\Email;
use stdClass;

class Post extends Model
{
    public PostImage $postImage;

    public ?string $image = null;
    public ?string $image2x = null;

    // the number of words in the content
    protected bool $toBreadCrumbAdded = false;
    protected array $response = array(
        'message' => '',
        'status' => true
    );
    private int $wordCount;
    private string $readingTime;

    public function __construct(array $properties = [])
    {
        parent::__construct($properties);
        $this->prepareData();

        $this->postImage = new PostImage();
        if (!empty($this->image)) {
            if (!str_contains($this->image, 'http')) {
                $image = $this->image;
                $this->image = $this->postImage->getImageSizeUrl($image, 600, 338);
                $this->image2x = $this->postImage->getImageSizeUrl($image, 1200, 676);
            }
        }
    }

    public function prepareData(): void
    {
        $this->loadWordCount();
        $this->setReadingTime();
        $this->loadVotes();
    }

    private function loadWordCount(): void
    {
        ob_start();
        View::post($this->getFileName());
        $content = ob_get_clean();
        $this->wordCount = TextHelper::getWordCount($content); // str_word_count(strip_tags($content));
    }

    public function getFileName(): string
    {
        return is_file(VIEWS_DIR . 'posts' . DS . $this->getId() . '.php')
            ? $this->getId()
            : $this->getName();
    }

    public function getWordCount(): int
    {
        return $this->wordCount;
    }

    private function setReadingTime()
    {
        $this->readingTime = ceil($this->wordCount / 250);
    }

    private function loadVotes(): void
    {
        $voteFile = USERDATA_DIR . '/votes/post_' . $this->getId() . '.json';
        $voteFileData = @file_get_contents($voteFile);

        if ($voteFileData) {
            $this->votes = json_decode($voteFileData);
        } else {
            $this->votes = new stdClass();
            $this->votes->up = 0;
            $this->votes->down = 0;
        }
    }

    public function getCreated(): string
    {
        return $this->created ?? '';
    }

    public function getModified(): string
    {
        return $this->getModifiedDate() ?? '';
    }

    public function getModifiedDate(): string
    {
        return File::getFileChangedDateTime($this->getFilePath(), 'Y-m-d H:i:s');
    }

    public function getFilePath(): string
    {
        return VIEWS_DIR . 'posts' . DS . $this->getFileName() . '.php';
    }

    public function getModifiedDateFormatted(): string
    {
        // var_dump($this->getFileName());
        return File::getFileChangedDateTime($this->getFilePath(), 'd.m.Y');
    }

    public function getCanonical(): string
    {
        $url = $this->getSlug();

        if (substr($url, -1, 1) === '/') {
            $url = substr($url, 0, -1);
        }

        return DOMAIN . $url . '.html';
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getFileNameFromSlug(): string
    {
        return str_replace('/', '', $this->getSlug());
    }

    public function getUrl(): ?string
    {
        return DOMAIN . $this->slug;
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

    private function respond()
    {
        echo json_encode($this->response);
        exit;
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
            $slug = CategoryCollection::getInstance()::getCategorySlugById($post->categories[0]);
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

    public function getReadingTime(): string
    {
        return $this->readingTime;
    }

    public function getTitleRawText(): string
    {
        return encode(strip_tags($this->getTitle()));
    }

    public function getTitle(): string
    {
        $title = $this->getSEOTitle();

        if (empty($title)) {
            $title = $this->get('title');
        }

        if (isset($this->_data['data_provider'])) {
            $data = include DATA_DIR . '' . $this->_data['data_provider'];
            $title = str_replace('{count}', count($data), $title);
        }
        return $title;
    }

    private function getSEOTitle()
    {
        return $this->_data['seo_title'] ?? '';
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

        $post = PostCollection::getInstance()->getPost((int)$id);

        try {
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

            // Cache::clear($post->getSlug());
        } catch (Exception $e) {

        }

        $this->response['status'] = true;
        $this->respond();
    }

}