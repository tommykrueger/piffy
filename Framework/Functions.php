<?php


function loadFunctions(): void
{
    $files = glob(__DIR__ . DS . 'functions/*.php');
    if ($files) {
        foreach ($files as $file) {
            include_once $file;
        }
    }
}

loadFunctions();