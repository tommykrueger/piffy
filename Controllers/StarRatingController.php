<?php

namespace Piffy\Controllers;

use Piffy\Collections\PostCollection;
use Exception;
use Piffy\Plugins\Newsletter\Models\Email;
use stdClass;

class StarRatingController
{
    private array $response = array(
        'message' => '',
        'status' => true
    );

    private string $dirName = USERDATA_DIR . DS . 'star-rating';

    public function __construct()
    {

    }

    public function save(): void
    {
        $data = $_REQUEST['data'];
        $d = json_decode(html_entity_decode(stripslashes($data)), false);

        @session_start();
        $IP = $_SERVER['REMOTE_ADDR'];

        if (!is_dir($this->dirName)) {
            mkdir($this->dirName);
        }

        $id = $d->id ?? null;
        $stars = $d->stars ?? null;

        if (is_null($id) || is_null($stars)) {
            $this->respond();
        }

        $stars = (int)$stars;


        // save the vote to local file
        $voteFile = $this->dirName . DS . 'post_' . $id . '.json';
        $voteFileData = @file_get_contents($voteFile);

        if ($voteFileData) {
            $votes = json_decode($voteFileData);
        }

        if (!isset($votes)) {
            $votes = new stdClass();
            $votes->stars = array_fill(0, 5, 0);
            //$votes->stars[$stars] += 1;
        }

        if (isset($d->stars)) {
            $votes->stars[$stars - 1] += 1;
        }

        @file_put_contents($voteFile, json_encode($votes));

        $post = PostCollection::getInstance()->getPost((int)$id);

        try {
            $emailData['id'] = $id;
            $emailData['post'] = $post;
            $emailData['stars'] = $d->stars;
            $email = new Email((object)[
                'recipient' => 'info@lachvegas.de',
                'emailData' => (object)$emailData,
                'subject' => 'Star Voting #' . date('Y-m-d'),
                'emailTemplate' => 'post_like',
            ]);
            $email->send();

            // Cache::clear($post->getSlug());
        } catch (Exception $e) {

        }

        $this->response['status'] = true;
        $this->respond();
    }

    private function respond(): void
    {
        echo json_encode($this->response);
        exit;
    }
}
