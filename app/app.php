<?php

namespace App;

use App\Framework\Autoloader;
use App\Framework\Cache;
use App\Framework\Debug;

require_once(__DIR__ . '/Framework/Functions.php');
require_once(__DIR__ . '/Framework/Autoloader.php');

/*
spl_autoload_register(function ($class) {
    // Adapt this depending on your directory structure
    $parts = explode('\\', $class);
    include end($parts) . '.php';
});
*/

// var_dump(DIRECTORY_SEPARATOR);
// var_dump(__NAMESPACE__);


$directories = [
    'collections',
    'controllers',
    'framework',
    'lib',
    'models',
    'plugins',
];


(new Autoloader())->registerNamespaces([
    'App\\',
    'App\\Framework\\',
    'App\\Collection\\',
    'App\\Models\\'
])->register();


// var_dump(getenv());

# include the framework classes
// require($path . '/framework/debug.php');
//require($path . '/framework/cache.php');
// require($path . '/framework/router.php');
//require($path . '/framework/view.php');
//require($path . '/framework/file.php');

# load the controllers
// require( $path . '/controllers/CategoryController.php' );

$path = __DIR__;

#require($path . '/controllers/AjaxController.php');
#require($path . '/controllers/AuthorController.php');
#require($path . '/controllers/CategoryController.php');
#require($path . '/controllers/ImageController.php');
#require($path . '/controllers/PageController.php');
#require($path . '/controllers/PostController.php');
#require($path . '/controllers/TagController.php');

# load the data collections
//require($path . '/collections/AuthorCollection.php');
//require($path . '/collections/PageCollection.php');
//require($path . '/collections/PostCollection.php');

# load the helpers
// require($path . '/models/helpers/DateHelper.php');

# load the models
#require($path . '/models/breadcrumb.php');
#require($path . '/models/category.php');
#require($path . '/models/image.php');
// require($path . '/models/newsletter.php');
#require($path . '/models/page.php');
// require( $path . '/models/person.php' );
#require($path . '/models/post.php');
#require($path . '/models/template.php');
#require($path . '/models/user.php');

Debug::startTime();

# change to true to activate caching
Cache::setActive(!DEBUG_MODE);

# include the data
#require($path . '/data/categories.php');
#require($path . '/data/pages.php');
#require($path . '/data/posts.php');

# include the routing
require($path . '/routes.php');

Debug::endTime();