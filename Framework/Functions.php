<?php

use App\Models\Category;
use Piffy\Framework\View;

function partial(string $path, mixed $data = null): void
{
    View::partial($path, $data);
}

/**
 * @return string
 */
function getVersion(): string
{
    return THEME_VERSION;
}

/**
 * Encode a string with quotes and double quotes. Used for json+ld fields and alt tags for <img>
 * @param $string
 * @return string
 */
function encode($string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Check weather we are on the homepage or not
 * @return bool
 */
function isHome(): bool
{
    return (isset($_SERVER['REQUEST_URI']) && in_array($_SERVER['REQUEST_URI'], ['', '/']));
}

/**
 * @param $data
 * @return bool
 */
function isCategory($data): bool
{
    return (isset($data->isCategory) && $data->isCategory);
}

/**
 * @param $data
 * @return bool
 */
function isArticle($data): bool
{
    return (isset($data->isArticle) && $data->isArticle);
}

/**
 * @param $data
 * @return string|string[]
 */
function getUrl($data): array|string
{
    $url = DOMAIN . $_SERVER['REQUEST_URI'];
    if (isset($data->slug)) {
        if (is_array($data->slug)) {
            $url = DOMAIN . $data->slug[0];
        } else {
            if ($data->slug[0] !== '/') {
                $url = DOMAIN . '/' . $data->slug;
            } elseif ($data->slug !== '/') {
                $url = DOMAIN . $data->slug;
            }
        }
    }

    /*
    else {
        $url = DOMAIN . '/' .$data->slug . '/';
    }
    */
    return str_replace(array('www.', 'http://'), array('', 'https://'), $url);
}

function getTitle($data): string
{
    if (has($data, 'seo_title')) {
        $string = $data->seo_title;
    } elseif (has($data, 'name')) {
        $string = $data->name;
    } else {
        $string = $data->title ?? '';
    }
    return encode($string);
}

function getDescription($data): string
{
    if (has($data, 'seo_description')) {
        $string = $data->seo_description;
    } elseif (has($data, 'content')) {
        $string = $data->content;
    } elseif (has($data, 'name')) {
        $string = $data->name;
    } else {
        $string = $data->title ?? '';
    }
    return encode($string);
}

function getKeywords($data): string
{

    $string = '';
    if (has($data, 'seo_keywords')) {
        $string = $data->seo_keywords;
    }
    return encode($string);
}

function getImage($data)
{
    if (isset($data->image) && !empty($data->image)) {
        return $data->image;
    }
    return false;
    // return DOMAIN . '/app/public/img/logo.png';
}


function slugify($string, $word_delimiter = '-'): string
{
    // $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $input);

    $slug = str_ireplace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $string);

    $slug = iconv('UTF-8', 'utf-8//IGNORE', $slug);
    $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
    $slug = strtolower(trim($slug, '-'));
    $slug = preg_replace("/[\/_|+ -]+/", $word_delimiter, $slug);
    return $slug;
}

function has($obj, $key): bool
{
    return (isset($obj->{$key}) && !empty($obj->{$key}));
}

function isPage($name): bool
{
    return (str_replace('/', '', $_SERVER['REQUEST_URI']) === $name);
}


function post($post, $data = null): void
{
    View::post($post, $data);
}

function view($post, $data = null): void
{
    View::render($post, $data);
}

function getCategoryUrl($data, $fullPath = true): string
{
    return Category::getUrl($data->id, $fullPath);
}