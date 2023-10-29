<?php

use App\Framework\File;

return [
    [
        'id' => 1,
        'title' => 'Homepage',
        'name' => 'homepage',
        'slug' => '/',
        'seo_title' => '',
        'seo_description' => '',
        'seo_keywords' => '',
        'created' => date('c', strtotime('2016-09-11 22:23:11')),
        'modified' => File::getFileChangedDateTime(APP_DIR . '/data/posts.php'),
    ],
    [
        'id' => 2,
        'title' => 'Kontakt',
        'name' => 'kontakt',
        'slug' => '/kontakt/',
        'created' => date('c', strtotime('2016-09-21 12:23:42')),
        //'modified' => File::getFileChangedDateTime(APP_DIR . '/views/kontakt.php'),
        'template' => 'page'
    ],
    [
        'id' => 3,
        'title' => 'Impressum',
        'name' => 'impressum',
        'slug' => '/impressum/',
        'created' => date('c', strtotime('2016-09-12 22:23:11')),
        //'modified' => File::getFileChangedDateTime(APP_DIR . '/views/impressum.php'),
        'template' => 'page'
    ],
    [
        'id' => 4,
        'title' => 'Datenschutzerklärung',
        'name' => 'datenschutz',
        'slug' => '/datenschutz/',
        'seo_title' => 'Datenschutzerklärung',
        'seo_description' => 'Allgemeiner Hinweis und Pflichtinformationen, Benennung der verantwortlichen Stelle, Die verantwortliche Stelle für die Datenverarbeitung auf dieser Website',
        'seo_keywords' => 'LachVegas, Datenschutzerklärung',
        'created' => date('c', strtotime('2016-09-12 22:23:11')),
        'template' => 'page'
    ],
    [
        'id' => 5,
        'title' => 'Partner',
        'name' => 'partner',
        'slug' => '/partner/',
        'created' => date('c', strtotime('2020-02-12 23:23:14')),
        'template' => 'page',
        'robots' => 'noindex, follow'
    ],
    [
        'id' => 7,
        'title' => 'Suche',
        'name' => 'suche',
        'slug' => '/suche/',
        'seo_title' => 'Suche',
        'robots' => 'noindex, follow'
        // 'template' => 'page'
    ],
    [
        'id' => 100000,
        'title' => '404 - Seite nicht gefunden',
        'name' => '404',
        'slug' => '/404/',
        'seo_title' => '404 Seite nicht gefunden',
        'seo_description' => '',
        'seo_keywords' => '',
        'template' => 'page',
        'created' => date('c', strtotime('2022-02-31 23:23:14')),
    ],
];