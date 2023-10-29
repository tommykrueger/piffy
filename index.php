<?php

const BASE_DIR = __DIR__;
const APP_DIR = BASE_DIR . '/app/';
const VIEWS_DIR = APP_DIR . '/Views/';
const PARTIALS_DIR = VIEWS_DIR . 'partials/';
define('DEBUG_MODE', (bool)strpos($_SERVER['HTTP_HOST'], 'local'));
const DEACTIVATE_ADS = false;
define('DOMAIN', (DEBUG_MODE ? 'http://' : 'https://') . $_SERVER['HTTP_HOST']);
const THEME_VERSION = '0.1.1';

const DS = DIRECTORY_SEPARATOR;

function isSecure(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
}

function isWWW(): bool
{
    return !(false === strpos($_SERVER['HTTP_HOST'], 'www.'));
}

// check for non https and www. domain calls and redirect them all
if (!DEBUG_MODE && (!isSecure() || isWWW())) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://piffy.de.local" . $_SERVER['REQUEST_URI']);
    exit;
}


// report all errors
error_reporting(-1);

// same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL & ~E_DEPRECATED & ~E_NOTICE);

require_once(APP_DIR . 'app.php');
