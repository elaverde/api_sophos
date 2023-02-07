<?php
declare(strict_types=1);

use App\Model\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
return function (App $app) {
    // Definir el middleware de autenticación
    $container = $app->getContainer();
    
    $app->group('/v1', function () use ($app,$container) {
        $authenticationMiddleware = new \App\Middleware\AuthMiddleware($container);
        /**
         * Endpoint para login de usuario administrador
         * 
         * Datos esperados:
         * email: Correo electrónico del usuario (requerido)
         * password: Contraseña del usuario (requerido)
         */
        /* Definición de una ruta para el punto final de inicio de sesión. */
        $app->post('/login', \App\Controllers\UserController::class . ':login');
        /**
         * Endpoint para crear un nuevo usuario administrador
         *
         * Datos esperados:
         * - name: Nombre del usuario (requerido)
         * - last_name: Apellido del usuario (requerido)
         * - email: Correo electrónico del usuario (requerido)
         * - password: Contraseña del usuario (requerido)
         */
        $app->post('/users', \App\Controllers\UserController::class . ':store')->add($authenticationMiddleware);;
        /**
         * Endpoint para actualizar un usuario administrador
         *
         * Datos esperados:
         * - name: Nombre del usuario (requerido)
         * - last_name: Apellido del usuario (requerido)
         * - email: Correo electrónico del usuario (requerido)
         * - password: Contraseña del usuario (requerido)
         */
        $app->put('/users/{id}', \App\Controllers\UserController::class . ':update');
        /**
         * Endpoint para eliminar un usuario administrador
         * 
         * Datos esperados:
         * - id: Identificador del usuario (requerido)
         */
        $app->delete('/users/{id}', \App\Controllers\UserController::class . ':delete');
        /**
         * Endpoint para obtener listar de usuarios en la url se espesifica el filtro
         * 
         * Datos esperados:
         * filter: Filtro de búsqueda (requerido)
         * search: Valor a buscar (requerido)
         */
        $app->get('/users/{filter}/{search}', \App\Controllers\UserController::class . ':index');
    });
};