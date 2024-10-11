<?php

namespace Piffy\Framework;

use App\Collections\PageCollection;
use App\Controllers\PageController;
use Piffy\Exceptions\RouteNotFoundException;

class Application
{
    public string $configFile = BASE_DIR . DS . 'config.php';

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

        $this->loadGlobalFunctions();
    }

    public function run(): void
    {
        Debug::startTime();

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
}

