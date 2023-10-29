<?php

namespace App\Models;

class Page
{
    private $_data;

    public function __construct(array $properties = [])
    {
        $this->_data = $properties;

        foreach($properties as $key => $value){
            $this->{$key} = $value;
        }
    }

    // magic methods!
    public function __set($property, $value){
        return $this->_data[$property] = $value;
    }

    public function __get(string $property): ?string {
        return array_key_exists($property, $this->_data)
            ? $this->_data[$property]
            : null;
    }

    public function render($name, $data = null)
    {

        $data = (object)$data ?? new stdClass();

        include(APP_DIR . '/data/categories.php');
        include(APP_DIR . '/data/posts.php');


        // somebody pressed the search button
        if ($name === 'suche') {
            if (isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
                $data->search = new stdClass();
                $data->search->query = $_REQUEST['s'];

                $query = strtolower($data->search->query);

                $filteredPosts = array_values(array_filter($posts, function ($post) use ($query) {
                    return (strpos(strtolower($post['title']), $query) || strpos(strtolower($post['content']), $query));
                }));

                for ($i = 0; $i < count($filteredPosts); $i++) {
                    $voteFile = APP_DIR . '/app/data/user-generated/votes/post_' . $filteredPosts[$i]['id'] . '.json';
                    $voteFileData = @file_get_contents($voteFile);

                    if ($voteFileData) {
                        $filteredPosts[$i]['votes'] = json_decode($voteFileData);
                    } else {
                        $filteredPosts[$i]['votes'] = new stdClass();
                        $filteredPosts[$i]['votes']->up = 0;
                        $filteredPosts[$i]['votes']->down = 0;
                    }
                }

                $data->posts = array_reverse($filteredPosts);
            } else {
                $data->content = 'Keine Daten vorhanden';
            }

            $data->title = 'Suche ... ';
        }


        // todo load the data
        if ($name === 'homepage') {

            $limit = 14;

            $categories = include(APP_DIR . '/data/categories.php');
            $posts = include(APP_DIR . '/data/posts.php');
            $tags = include(APP_DIR . '/data/tags.php');

            $data->postCount = count($posts);

            $data->pagination = new stdClass();
            $data->pagination->totalPages = ceil(count($posts) / $limit);
            $data->pagination->currentPage = $_REQUEST['page'] ?? 1;
            $data->pagination->url = '/';


            $posts = array_reverse($posts);

            $data->postsRatgeber = [];
            $data->postsPolitikGesellschaft = [];
            $data->postsBerufKarriere = [];
            $data->postsBeziehungPartnerschaft = [];

            for ($i = 0; $i < count($posts); $i++) {

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

                if (!empty($posts[$i]['image'])) {
                    $posts[$i]['image'] = DOMAIN . '/app/public/img/posts/' . $posts[$i]['image'];
                }

                // load latest 12 posts which belong to category "Ratgeber"
                if (count($data->postsRatgeber) < 12 && in_array(430, $posts[$i]['categories'])) {
                    $data->postsRatgeber[] = (object)$posts[$i];
                }

                // load latest 6 posts which belong to category "Politik & Gesellschaft"
                if (count($data->postsPolitikGesellschaft) < 6 && in_array(390, $posts[$i]['categories'])) {
                    $data->postsPolitikGesellschaft[] = (object)$posts[$i];
                }

                // load latest 6 posts which belong to category "Beruf & Karriere"
                if (count($data->postsBerufKarriere) < 6 && in_array(50, $posts[$i]['categories'])) {
                    $data->postsBerufKarriere[] = (object)$posts[$i];
                }

                // load latest 6 posts which belong to category "Beziehung & Partnerschaft"
                if (count($data->postsBeziehungPartnerschaft) < 6 && in_array(60, $posts[$i]['categories'])) {
                    $data->postsBeziehungPartnerschaft[] = (object)$posts[$i];
                }

                // ...
            }


            $posts = array_slice($posts, 0, $limit);

            $data->posts = $posts;
            $data->title = 'Seite ' . $data->pagination->currentPage . ' ' . $data->title;
        }


        if ($name === 'admin-import') {
            include(APP_DIR . '/data/posts.php');
            $data->posts = $posts;
            $data->title = "Admin Import";
        }

        render($name, $data);
    }

}