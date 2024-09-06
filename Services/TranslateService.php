<?php

namespace Piffy\Services;

class TranslateService {

    protected static ?TranslateService $_instance = null;

    protected static array $acceptedLanguages = [
        'de',
        'en'
    ];

    protected static array $languages = [
        'de' => [
            'name' => 'Deutsch',
            'locale' => 'de_DE',
            'contentLanguage' => 'de-de'
        ],
        'en' => [
            'name' => 'English',
            'locale' => 'en_US',
            'contentLanguage' => 'en-us'
        ],
    ];

    protected static string $defaultLanguage = 'de';

    protected static string $currentLanguage;

    protected function __construct() {
        @session_start();
        self::detectUserLanguage();
        self::loadTranslations();
    }

    public static function getInstance(): ?TranslateService
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


    public static function detectUserLanguage(): void
    {
        // check if there an explicit url lang parameter
        if (isset($_REQUEST['lang']) && in_array($_REQUEST['lang'], self::$acceptedLanguages)) {
            $lang = $_REQUEST['lang'];
        }

        // check if there is a session with active language
        elseif (isset($_SESSION['lang'])) {
            $lang = $_SESSION['lang'];
        }

        // default: check the browser user agent
        else {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'de', 0, 2);
        }

        $_SESSION['lang'] = $lang;

        self::$currentLanguage = in_array($lang, self::$acceptedLanguages) ? $lang : self::$defaultLanguage;
    }

    private static function loadTranslations(): void
    {
        foreach (self::$languages as $locale => $language) {
            if (file_exists(APP_DIR . '/data/lang/' . $locale . '.php')) {
                $translationData = include_once(APP_DIR . '/data/lang/' . $locale . '.php');
                self::$languages[$locale]['translations'] = $translationData;
            }
        }
    }

    public static function getLanguage($active = true): void
    {
        self::$isActive = $active;
    }

    public static function setLanguage($active = true): void
    {
        self::$isActive = $active;
    }

    public static function getActiveLanguage(): string
    {
        $language = (object)self::$languages[$_SESSION['lang'] ?? DEFAULT_LANG];
        return $language->locale;
    }

    public static function getLanguages(): array
    {
        return self::$languages;
    }

    public static function translate(string $string, array $values = [], string $lang = DEFAULT_LANG): string
    {
        $lang = self::$currentLanguage;
        $translations = self::$languages[$lang]['translations'];

        if (isset($translations[$string])) {
            return sprintf($translations[$string], ...$values);
        }

        return $translations[$string] ?? $string;
    }

    public static function getContentLanguage(): string
    {
        $language = (object)self::$languages[$_SESSION['lang'] ?? DEFAULT_LANG];
        return $language->contentLanguage;
    }

}

