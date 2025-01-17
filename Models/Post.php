<?php

namespace Piffy\Models;

use App\Collections\CategoryCollection;
use App\Models\PostImage;
use Piffy\Collections\PostCollection;
use Piffy\Framework\File;
use Piffy\Framework\Model;
use Piffy\Framework\View;
use Piffy\Helpers\TextHelper;
use Piffy\Plugins\Comments\Models\Enum\CommentStatus;
use Piffy\Services\SchemaService;
use stdClass;

class Post extends Model
{
    public string $schema = 'post';

    public int $id = 0;
    public string $title = '';
    public string $subtitle = '';
    public string $seo_title = '';
    public string $slug = '';
    public string $excerpt = '';

    public bool $repeatable = false;



    public string $created = '';
    public string $modified = '';

    public PostImage $postImage;

    public bool $toBreadCrumbAdded = false;

    // the number of words in the content
    public int $wordCount = 0;

    public string $readingTime = '';

    public string|false $image = '';
    public string|false $image2x = '';


    public array $relatedPosts = [];

    public array $categories = [];

    public object $comments;

    public string $createdDate;

    public function __construct(array $properties = [])
    {
        parent::__construct($properties);

        // $this->prepareData();

        $this->modified = $this->getModifiedDate();
        $this->postImage = new PostImage();
        if (!empty($this->image)) {
            if (!str_contains($this->image, 'http')) {
                $image = $this->image;
                $this->image = $this->postImage->getImageSizeUrl($image, 600, 338);
                $this->image2x = $this->postImage->getImageSizeUrl($image, 1200, 676);
            }
        }

        $this->loadVotes();
       //  $this->loadWordCount();
        $this->setReadingTime();
    }


    /**
     * Load all extra data required for this model
     *
     * @return void
     */
    public function prepareData(): void
    {
        $this->loadWordCount();
        $this->setReadingTime();
        $this->loadVotes();
        $this->loadRelatedPosts();
        $this->loadComments();

        // $this->loadCategories();
        // $this->loadRelations();

        // @todo
        // loadComments
        // loadPostLikes
        // loadPostListLikes
        // loadRelatedPosts
        // load Tags
        // load Categories
        // load Authors
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getUrl(): ?string
    {
        return DOMAIN . $this->slug;
    }

    public function getCreated(): string
    {
        return $this->created ?? '';
    }

    public function getModified(): string
    {
        return $this->getModifiedDate() ?? '';
    }

    public function getCanonical(): string
    {
        $url = $this->getSlug();

        if (substr($url, -1, 1) === '/') {
            $url = substr($url, 0, -1);
        }

        return DOMAIN . $url . '.html';
    }


    private function loadWordCount(): void
    {
        ob_start();
        View::post($this->getFileName());
        $content = ob_get_clean();
        $this->wordCount = TextHelper::getWordCount($content); // str_word_count(strip_tags($content));
        ob_clean();


        /*
        if ($this->getFileName()) {
            $data = file_get_contents($this->getFilePath());
            $this->wordCount = TextHelper::getWordCount($data);
        }
        */


        // Specify the file path
        //$file_path = 'path/to/your/file.txt';
        // Read the file content
        //$file_content = file_get_contents($file_path);
        // Count the words

        // Display the word count
        // echo "The file contains $word_count words.";
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

    public function getFileName(): string
    {
        $folder = VIEWS_DIR . 'posts' . DS;
        if (is_file($folder . $this->getId() . '.php')) {
            return $this->getId();
        }

        if (is_file($folder . $this->getName() . '.php')) {
            return $this->getName();
        }

        if (is_file($folder . $this->getFileNameFromSlug() . '.php')) {
            return $this->getFileNameFromSlug();
        }

        return '';
    }

    public function getWordCount(): int
    {
        return $this->wordCount;
    }

    public function getCreatedDateFormatted(): string
    {
        return date('d.m.Y', strtotime($this->created));
    }

    public function getModifiedDate(): string
    {
        return File::getFileChangedDateTime($this->getFilePath(), 'Y-m-d H:i:s');
    }

    public function getModifiedDateFormatted(): string
    {
        // var_dump($this->getFileName());
        return File::getFileChangedDateTime($this->getFilePath(), 'd.m.Y');
    }

    public function getFilePath(): string
    {
        return VIEWS_DIR . 'posts' . DS . $this->getFileName() . '.php';
    }

    public function getFileNameFromSlug(): string
    {
        return str_replace('/', '', $this->getSlug());
    }

    private function setReadingTime(): void
    {
        $this->readingTime = ceil($this->wordCount / 250) . ' min';
    }

    public function getCategories(): array
    {
        return CategoryCollection::getInstance()->getByIds($this->categories);
    }

    public function generateImage($data = null, $isAjax = true): bool
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




    public function getReadingTime(): string
    {
        return $this->readingTime;
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

    public function getTitleRawText(): string
    {
        return encode(strip_tags($this->getTitle()));
    }

    private function getSEOTitle()
    {
        return $this->_data['seo_title'] ?? '';
    }

    public function loadRelatedPosts(): Post
    {
        $this->relatedPosts = PostCollection::getInstance()->getRelatedPosts($this);
        $this->relatedPosts = array_slice($this->relatedPosts,0, 8);
        return $this;
    }

    public function loadComments(): Post
    {
        $commentsFile = USERDATA_DIR . '/comments/post_' .  $this->getId() .  '.json';
        $commentsFileData = @file_get_contents($commentsFile);

        if ($commentsFileData) {
            $this->comments = json_decode($commentsFileData);

            $this->comments->comments = array_values(array_filter($this->comments->comments, function ($comment) {
                if ($comment->status === CommentStatus::PUBLIC) {
                    return true;
                }
                return false;
            }));

            usort($this->comments->comments, function($a, $b){
                return ($a->created < $b->created);
            });
        }

        return $this;
    }

    public function loadRelations(): void
    {
        $schema = SchemaService::getInstance()->getSchemas($this->schema);

        if (isset($schema->relations)) {
            foreach ($schema->relations as $relation) {
                $relation = (object)$relation;
                //var_dump($relation);

                $model = $relation->model;

                    //var_dump($model);

                    $prop = $this->get($relation->property);

                    //var_dump($prop);

                    if (!isset($relation->collection)) {
                        continue;
                    }


                    if (is_array($prop)) {
                        $this->{$relation->property} = [];

                        foreach ($prop as $item) {

                            $this->{$relation->property}[] = $relation->collection::getInstance()->getById($item);
                        }
                    }

                    //var_dump($prop);


            }
        }
    }

    public function loadCategories(): void
    {
        if (empty($this->categories)) {
            return;
        }
        for($i=0; $i<count($this->categories); $i++) {
            $cat = CategoryCollection::getInstance()->getById($this->categories[$i]);
            if (!$cat) {

            }
            $this->categories[$i] = $cat;
        }
    }
}