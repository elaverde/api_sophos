<?php
session_start();
date_default_timezone_set("America/Bogota");
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;
use Jenssegers\Blade\Blade;
require __DIR__ . '/vendor/autoload.php';

/* Cargando el archivo .env. */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/* Este es el código que se conecta a la base de datos. */
require __DIR__ . '/config.php';
$app = new \Slim\App($config['slim']);
$container = $app->getContainer();
$capsule = new Capsule;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

/* Cargando todas las rutas desde la carpeta de rutas. */
$routes = array("users");
foreach ($routes as $route) {
    $file = __DIR__ . '/app/routes/' . $route . '/route.php';
    if (file_exists($file)) {
        $routes = require $file;
        $routes($app);
    }
}


// Documentación generada por Swagger-PHP


/* Capturar cualquier excepción que pueda ocurrir en la aplicación. */
try {
    $app->run();
} catch (Exception $e) {
    die(json_encode(array("status" => "failed", "message" => "allowed" . $e)));
}
?>