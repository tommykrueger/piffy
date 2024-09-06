<?php

namespace Piffy\Services;

use Error;
use Piffy\Framework\DB_PDO;

class SetupService {

    //private DB_PDO $db;

    public function __construct()
    {
        //$this->db = new DB_PDO();
    }

    public function start(): void
    {
        try {
            // prepare the database tables from database folder
            $dir = BASE_DIR . DS .'database' . DS;

            if (!is_dir($dir)) {
                die('No database folder found');
            }

            $sqlFiles = [];
            foreach (glob($dir . "*.sql") as $filename) {
                echo $filename . " found <br />";
                if (is_file($filename)) {
                    $sqlFiles[] = $filename;
                }
            }

            echo '___ <br>';

            if (empty($sqlFiles)) {
                die("No sql files found");
            }

            $db = new DB_PDO();

            foreach ($sqlFiles as $file) {
                $fileData = file_get_contents($file);

                echo 'trying to create new database table <br>';

                $statement = $db->prepare($fileData);

                if (!$statement) {
                    echo $statement->errorCode();
                }

                $result = $statement->execute();

                if ($result) {
                    echo $file . ' executed successfully <br>';
                }
                $statement->closeCursor();
            }
        } catch (Error $e) {
            die($e);
        }

    }


    /*
     * try to import data to database from json files
     */
    public function import(): void
    {
        // post votes
        $dir = DATA_DIR . 'user-generated' . DS . 'votes' . DS;
        $files = $this->readFilesFromFolder($dir);

        foreach ($files as $file) {
            $data = @file_get_contents($file);

            if (!$data) {
                continue;
            }

            $data = json_decode($data);

            $post_id = (int) str_replace('post_', '', basename($file, '.json'));

            $query = 'INSERT INTO post_votes (post_id, up, down, created, updated) VALUES ('. $post_id .','. $data->up .','. $data->down .', now(), now())';

            $db = new DB_PDO();
            $statement = $db->prepare($query);

            if (!$statement) {
                echo $statement->errorCode();
            }

            $statement->execute();
            $statement->closeCursor();
        }


        // post views
        $dir = DATA_DIR . 'user-generated' . DS . 'pageviews' . DS . 'post' .DS;
        $files = $this->readFilesFromFolder($dir);

        foreach ($files as $file) {
            $data = @file_get_contents($file);

            if (!$data) {
                continue;
            }

            $data = json_decode($data);

            $post_id = (int) basename($file, '.json');

            if (!$post_id || !isset($data->pageviews)) {
                continue;
            }

            $query = 'INSERT INTO post_views (post_id, views, created,updated) VALUES ('. $post_id .','. $data->pageviews .', now(), now())';

            $db = new DB_PDO();
            $statement = $db->prepare($query);

            if (!$statement) {
                echo $statement->errorCode();
            }

            try {
                $statement->execute();
                $statement->closeCursor();
            } catch (\Throwable $e) {
                echo $query;
                echo $e->getMessage();
            }

        }



        // post views
        $dir = DATA_DIR . 'user-generated' . DS . 'list-likes' . DS . 'post' .DS;
        $files = $this->readFilesFromFolder($dir);

        foreach ($files as $file) {
            $data = @file_get_contents($file);

            if (!$data) {
                continue;
            }

            $data = json_decode($data);

            $post_id = (int) basename($file, '.json');

            if (!$post_id || !isset($data->pageviews)) {
                continue;
            }

            $query = 'INSERT INTO post_views (post_id, views, created, updated) VALUES ('. $post_id .','. $data->pageviews .', now(), now())';

            $db = new DB_PDO();
            $statement = $db->prepare($query);

            if (!$statement) {
                echo $statement->errorCode();
            }

            try {
                $statement->execute();
                $statement->closeCursor();
            } catch (\Throwable $e) {
                echo $query;
                echo $e->getMessage();
            }

        }
    }

    private function readFilesFromFolder(string $dir, string $pattern = '*.json'): array
    {
        $files = [];

        if (is_dir($dir)) {
            foreach (glob($dir . $pattern) as $filename) {
                if (is_file($filename)) {
                    $files[] = $filename;
                }
            }
        }

        return $files;
    }
}

