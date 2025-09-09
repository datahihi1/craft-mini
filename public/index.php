<?php

/*
| Basic Configuration
|--------------------------------------------------------------------------
*/

use Craft\Application\App;

ob_start();
define('CRAFT_RUN', microtime(true));

/*
| Define ROOT_DIR (Base of the framework)
|------------------------------------------------------------------------------------------------
| This defines the root directory of the application.
| It checks if the directory exists and is readable.
| If not, it returns a 500 error.
|------------------------------------------------------------------------------------------------
*/
if (!defined('ROOT_DIR')) {
    $rootDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    if (!is_dir($rootDir) || !is_readable($rootDir)) {
        http_response_code(500);
        die('Application root directory not accessible');
    }
    /** Define the root directory constant of CraftLite application */
    define('ROOT_DIR', $rootDir);
}

/*
| Define INDEX_DIR (Optional - Base of entry file)
*/

/*
| Autoloading
|------------------------------------------------------------------------------------------------
| This loads the Composer autoloader to include all dependencies.
| If the autoloader is not found, it returns a 500 error.
|------------------------------------------------------------------------------------------------
*/
$autoloadFile = ROOT_DIR . '/vendor/autoload.php';
if (!file_exists($autoloadFile)) {
    http_response_code(500);
    die('Composer autoloader not found. Please run "composer install"');
}
require_once $autoloadFile;


/*
| Initialize the Craft web application
|------------------------------------------------------------------------------------------------
| This sets up the application environment and prepares it for web requests.
|------------------------------------------------------------------------------------------------
*/
App::initializeWeb(ROOT_DIR . '/public/logs/');

/*
| Boot the Craft web application
|------------------------------------------------------------------------------------------------
| This starts the application and handles the request.
| It returns the response to be sent to the client.
|------------------------------------------------------------------------------------------------
*/
App::bootWeb();