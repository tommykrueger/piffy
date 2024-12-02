<?php

namespace Piffy\Controllers;

use App\Collections\PostCollection;
use Exception;
use Piffy\Enum\Events;
use Piffy\Framework\Cache;
use Piffy\Plugins\Comments\Models\Enum\CommentStatus;
use Piffy\Plugins\Newsletter\Models\Email;
use stdClass;

class CommentController
{
    private string $dirName = USERDATA_DIR . DS . 'comments';

    protected array $response = array(
        'message' => '',
        'status' => true
    );

    public function __construct()
    {
        // todo
    }

    public function addComment($id): void
    {
        if (empty($id)) {
            return;
        }

        @session_start();
        $IP = $_SERVER['REMOTE_ADDR'];
        $_SESSION['session_ip'] = $IP;

        $data = $_REQUEST['data'];
        $request = (object)json_decode($data);

        // save the vote to local file
        $file = $this->dirName . DS . 'post_' . $id . '.json';
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

            $commentId = count($comments->comments) + 1;

            $comments->comments[] = [
                'id' => $commentId,
                'parent' => $request->parent ?? 0,
                'created' => date('Y-m-d H:i:s'),
                'text' => $request->text,
                'author' => $request->author,
                'likes' => 0,
                'dislikes' => 0,
                'status' => CommentStatus::PENDING
            ];

        }


        @file_put_contents($file, json_encode($comments));

        Cache::clear($_SERVER['HTTP_REFERER']);

        try {

            $post = PostCollection::getInstance()->getPost($id);
            if ($post) {
                $email = new Email((object)[
                    'recipient' => 'info@lachvegas.de',
                    'subject' => 'Neuer Kommentar auf ' . DOMAIN,
                    'emailTemplate' => 'new_comment',
                    'emailData' => [
                        'postID' => $id,
                        'postTitle' => $post->title,
                        'postUrl' => DOMAIN . $post->slug,
                        'comment' => $request->text,
                        'activateLink' => 'activate-comment/' . $id . '/' . $commentId . '?do=it'
                    ]
                ]);
                $email->send();

                event(Events::COMMENT_POSTED, $post);
            }
        } catch (Exception $e) {

        }

        $this->response['status'] = true;
        $this->respond();
    }

    private function respond(): void
    {
        // @todo move to parent Controller
        echo json_encode($this->response);
        exit;
    }

    // activate the comment
    public function activateComment($postId, $commentId): void
    {
        if (!$commentId || !$postId) {
            return;
        }

        $parts = parse_url($_SERVER['REQUEST_URI']);
        if (isset($parts['query'])) {
            if ($parts['query'] !== 'do=it') {
                return;
            }
        }

        $file = $this->dirName . DS . 'post_' . $postId . '.json';
        $fileData = @file_get_contents($file);

        if ($fileData) {
            $comments = json_decode($fileData);

            foreach ($comments->comments as $key => $val) {
                if ($comments->comments[$key]->id === (int)$commentId) {
                    $comments->comments[$key]->status = CommentStatus::PUBLIC;
                }
            }
            @file_put_contents($file, json_encode($comments));
        }

    }
}