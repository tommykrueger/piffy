<?php

namespace Piffy\Framework;

use App\Collections\PageCollection;
use App\Controllers\PageController;
use Exception;
use Piffy\Exceptions\RouteNotFoundException;
use Piffy\Services\EventService;
use Piffy\Services\SchemaService;

class Application
{
    public string $configFile = BASE_DIR . DS . 'config.php';

    public array $serviceInstances = [];

    private EventService $eventService;

    private SchemaService $schemaService;

    public function __construct()
    {
        // @todo register classes
    }

    public function init(): void
    {
        if (!file_exists($this->configFile)) {
            exit('Error: Config file does not exist: ' . $this->configFile);
        }
        require_once($this->configFile);

        // report all errors
        error_reporting(-1);

        // same as error_reporting(E_ALL);
        ini_set('error_reporting', E_ALL & ~E_DEPRECATED & ~E_NOTICE);

        (new Autoloader())->registerNamespaces([
            'App\\',
            'App\\Collections\\',
            'App\\Controllers\\',
            'App\\Models\\',
            'Piffy\\'
        ])->register();

        define("USERDATA_DIR", BASE_DIR . DS . 'userdata' . DS . ENVIRONMENT ?? 'local' . DS);

        Debug::startTime();


        $this->schemaService = SchemaService::getInstance();
        $this->registerSchemas();

        $this->loadGlobalFunctions();
        $this->registerRoutes();
        $this->registerEvents();

        $file = BASE_DIR . DS . 'Piffy' . DS . 'constants.php';
        if (file_exists($file)) {
            include_once $file;
        }
    }

    public function run(): void
    {
        try {
            Router::execute($_SERVER['REQUEST_URI']);
        } catch (RouteNotFoundException $e) {
            header("HTTP/1.1 404 Not Found");
            (new PageController)->render(PageCollection::getInstance()->getPageByName('404'));
        }

        Debug::endTime();
    }

    public function loadGlobalFunctions(): void
    {
        $files = glob(__DIR__ . DS . '../Functions/*.php');
        if ($files) {
            foreach ($files as $file) {
                include_once $file;
            }
        }
    }

    private function registerService(): void
    {
        $serviceFiles = glob(BASE_DIR . DS . 'Piffy' . DS .'Services' . DS . '*.php');
        if ($serviceFiles) {
            foreach ($serviceFiles as $file) {
                var_dump($file);
                include_once $file;
                // include_once PLUGINS_DIR . DS . 'Services' . DS . $file . '.php';

                try {

                    //$namespace = "Piffy\Services\\";
                    $filename = pathinfo($file);
                    //var_dump($filename);
                    //$f = $namespace . $filename['filename'];
                    new $filename['filename'];
                } catch (Exception $e) {
                    Log::warning($e->getMessage());
                }

                // $this->services[] = new EmailService();
            }
        }
    }

    public function registerServices(array $serviceList = []): void
    {
        foreach ($serviceList as $service) {
            $this->serviceInstances[$service::class] = $service;
        }
    }

    public static function getServiceInstance(string $serviceName)
    {
        return self::$serviceInstances[$serviceName];
    }

    private function registerEvents(): void
    {
        $this->eventService = new EventService();

        $file = BASE_DIR . DS . 'Piffy' . DS . 'events.php';
        if (!file_exists($file)) {
            return;
        }

        $events = include_once $file;

        foreach ($events as $event => $eventListeners) {
            foreach ($eventListeners as $eventListener) {
                EventService::registerEventListener($event, $eventListener);
            }
        }

    }

    public function registerRoutes(): void
    {
        $files = glob(__DIR__ . DS . '../Routes/*.php');
        if ($files) {
            foreach ($files as $file) {
                include_once $file;
            }
        }
    }

    public function registerSchemas(): void
    {
        $schemas = glob(__DIR__ . DS . '../Schemas/*.json');
        if ($schemas) {
            foreach ($schemas as $schema) {
                $name = pathinfo($schema);
                SchemaService::getInstance()->registerSchema($name['filename'], $schema);
            }
        }
    }
}

