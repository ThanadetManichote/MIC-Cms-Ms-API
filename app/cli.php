<?php
use Phalcon\Loader;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Di\FactoryDefault\Cli as CliDI;

use Phalcon\Mvc\Collection\Manager;
use Phalcon\Db\Adapter\MongoDB\Client;

use Phalcon\Http\Client\Request as Curl;

//Set timezone
date_default_timezone_set("Asia/Bangkok");

// Using the CLI factory default services container
$di = new CliDI();

// Load the configuration file (if any)
$configFile = __DIR__ . "/config/config.php";

if (is_readable($configFile)) {
    $config = include $configFile;
    $di->set("config", $config);
}

// Load the message file (if any)
$messageFile = __DIR__ . "/config/message.php";

if (is_readable($messageFile)) {
    $message = include $messageFile;
    $di->set("message", $message);
}

//add repositories
$di->set('repository', function () {
    $repository =  new App\Repositories\Repositories();
    return $repository;
});

// Register a "myLibrary" service in the container
$di->set('myLibrary', function () {
    $myLib =  new App\Library\MyLibrary();
    return $myLib;
});

// Register a "mongoService" service in the container
$di->set('mongoService', function () {
    $mongoService =  new App\Services\MongoService();
    return $mongoService;
});

// Register a "saleAccountService" service in the container
$di->set('saleService', function () {
    $saleService =  new App\Services\SaleService();
    return $saleService;
});

// Register a "curl" service in the container
$di->set('curl', function () {
    $curl = Curl::getProvider();
    return $curl;
});

// Register a "model" service in the container
$di->set('model', function () {
    $model =  new App\Models\Models();
    return $model;
});

// Initialise the mongo DB connection.
$di->setShared('mongo', function () {
    /** @var \Phalcon\DiInterface $this */
    $config = $this->getShared('config');

    if (!$config->database->mongo->username || !$config->database->mongo->password) {
        $dsn = 'mongodb://' . $config->database->mongo->host.":". $config->database->mongo->port;
    } else {
        $dsn = sprintf(
            'mongodb://%s:%s@%s:%s/%s',
            $config->database->mongo->username,
            $config->database->mongo->password,
            $config->database->mongo->host,
            $config->database->mongo->port,
            $config->database->mongo->dbname
        );
    }

    $mongo = new Client($dsn);

    return $mongo->selectDatabase($config->database->mongo->dbname);
});

$di->set('collectionManager', function () {
    return new Manager();
}, true);


/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new Loader();
$loader->registerDirs(
    [
        __DIR__ . "/tasks",
        __DIR__ . "/repositories",
        __DIR__ . "/library",
        __DIR__ . "/models",
        __DIR__ . "/services",
    ]
);

$loader->registerNamespaces(array(
    'App\\Repositories' => __DIR__ . '/repositories/',
    'App\\Library'      => __DIR__ . '/library/',
    'App\\Services'     => __DIR__ . '/services/',
    'App\\Models'       => __DIR__ . '/models/',
    'App\\Tasks'        => __DIR__ . '/tasks/',
));
$loader->register();

//Load vendor
include __DIR__.'/../vendor/autoload.php';

// Create a console application
$console = new ConsoleApp();
$console->setDI($di);

/**
 * Process the console arguments
 */
$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments["task"] = $arg;
    } elseif ($k === 2) {
        $arguments["action"] = $arg;
    } elseif ($k >= 3) {
        $arguments["params"][] = $arg;
    }
}

try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();

    exit(255);
}