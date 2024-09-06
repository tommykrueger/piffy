<?php


namespace Piffy\Framework;

class FunctionsLoader
{
    public function loadFunctions(string $message = ''): void
    {
        $files = glob(__DIR__ . DS . 'functions/*.php');
        if ($files) {
            foreach ($files as $file) {
                include_once $file;
            }
        }
    }
}
