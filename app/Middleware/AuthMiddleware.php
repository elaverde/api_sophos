<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Exception;

class AuthMiddleware
{
    public function __invoke($request, $response, $next)
    {
        $header = $request->getHeader('Authorization');
        if (!$header) {
            return $response->withJson(['error' => 'Token not provided'], 401);
        }

        $token = str_replace('Bearer ', '', $header[0]);
        $key = $_ENV['JWT_KEY'];
        try {
            //encode y decode
            $decode = JWT::decode($token, new Key($key,'HS256'));

            /* Procese los datos del usuario aquí.      HS256*/
        } catch (Exception $e) {
            return $response->withJson(['error' => 'Token invalid'], 401);
        }

        $response = $next($request, $response);
        return $response;
    }
}