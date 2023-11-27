<?php

namespace Piffy\Framework;

class Autoloader
{
    private array $namespaces;

    public static function register2(array $directories): void
    {
        if (empty($directories)) {
            return;
        }

        foreach ($directories as $directory) {
            $path = APP_DIR . $directory . '/*.php';
            $files = glob($path);
            //var_dump($files);
            foreach ($files as $file) {
                self::load($file);
            }

            // register php files in directory


            // register php files in subdirectories
            //$path = APP_DIR . $directory . '/**/*.php';
            //$files = glob($path);
            //var_dump($files);
        }


        //$dirs = array_filter(glob("*"), 'is_dir');


        /*
        spl_autoload_register(static function ($class) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
        */
    }

    public static function register3(): void
    {
        spl_autoload_register(static function ($class) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });

        /*
        spl_autoload_register(function ($class) {

            var_dump($class);
            // project-specific namespace prefix
            $prefix = 'App\\';

            // base directory for the namespace prefix
            $base_dir = __DIR__ . '../app/';

            // does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // no, move to the next registered autoloader
                return;
            }

            // get the relative class name
            $relative_class = substr($class, $len);

            // replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $file = str_replace('\\', '/', $relative_class) . '.php';

            // if the file exists, require it
            if (file_exists($file)) {
                require $file;
            }
        });
        */
    }

    public function registerNamespaces(array $namespaces): autoloader
    {
        $this->namespaces = $namespaces;
        return $this;
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'make']);
    }

    public function make(string $class): void
    {
        $className = $class;
        foreach ($this->namespaces as $namespace) {
            // $prefix = 'App\\Framework\\';
            $prefix = $namespace;
            //print_r($prefix . '<br>');
            //print_r('namespace: ' . $namespace . '<br>');

            if (!substr($className, 0, 17) === $prefix) {
                // return;
            }


            $class = substr($className, strlen($prefix));
            //print_r($class . '<br>');
            //print_r('D: ' . $class . '<br>');
            $namespace_dir = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
            //print_r($namespace_dir . '<br>');
            // $namespace_dir = strtolower($namespace_dir);
            //print_r($namespace_dir . '<br>');
            $location = BASE_DIR . DIRECTORY_SEPARATOR . $namespace_dir . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

            //print_r('location: ' . $location . '<br>');
            //print_r($location . '<br>');

            if (is_file($location)) {
                //print_r('LOAD: ' . $location . '<br>');
                require_once($location);
            }
        }

    }

    /**
     * Das eigentliche Autoloading.
     *
     * @param $name
     */
    public function handle(string $name): void
    {
        $parts = explode('\\', $name);
        var_dump($this->namespaces[$parts[0]]);

        // Den obersten Namensraum der Klasse prüfen
        if (isset($this->namespaces[$parts[0]])) {
            // ok wir sind zuständig
            var_dump($this->namespaces[$parts[0]]);
            exit;

            // Zielpfad
            $path = $this->namespaces[$parts[0]];

            // den obersten Namensraum entfernen
            array_shift($parts);

            $fileName = $path . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';

            if (file_exists($fileName)) {
                require_once $fileName;
            }
        }
    }
}