<?php declare(strict_types=1);

session_start();

ini_set("memory_limit", "512M");
ini_set("post_max_size", "256M");
ini_set("upload_max_filesize", "256M");
ini_set('display_errors', 1);

header("Content-Type: application/json");
date_default_timezone_set("Europe/Zurich");
error_reporting(E_ALL);

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/Utils.php";
require_once __DIR__ . "/Container.php";
require_once __DIR__ . "/JWTMiddleware.php";

use Bramus\Router\Router;
use PR24\Dependencies\Container;

$container = new Container();

$router = new Router();
$router->setNamespace("PR24\Controller");
$router->setBasePath("/api");

$container->setupRoutes($router);

$router->run();