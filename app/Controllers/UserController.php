<?php
namespace App\Controllers;

use App\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;
use Firebase\JWT\JWT;

class UserController
{
    
    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $user = User::where('email', $data['email'])->first();
        
        if (!$user) {
            return $response->withJson(['error' => 'Invalid credentials'], 401);
        }
        if (!password_verify($data['password'], $user->password)) {
            return $response->withJson(['error' => 'Invalid credentials'], 401);
        }
        /* Generamos el token. */
        $now = strtotime('now');
        $key = $_ENV['JWT_KEY'];
        $playload =[
            'exp' => $now + ( 3600 * 60 * 24 * 365),// 1 year
            'iat' => $now,
            'sub' => $user->id
        ];
        $token = JWT::encode($playload, $key, 'HS256');
        return $response->withJson(['token' => $token]);
    }
    private function validateData(array $data): bool
    {
        if (!isset($data['name']) || !isset($data['last_name']) || !isset($data['email'])) {
            return false;
            
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }
    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        // Validate input data
        
        if (!$this->validateData($data)) {
            return $response->withJson(['error' => 'Bad Request'], 400);
        }
        if (strlen($data['password']) < 8) {
            return $response->withJson(['error' => 'Password must be at least 8 characters'], 400);
        }
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        /**
         * Validamos que el usuario no exista
         */
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser) {
            return $response
                ->withStatus(400)
                ->withJson([
                    'message' => 'A user with that email already exists.'
                ]);
        }
        $admin = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $password,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        //quitamos el password para que no se muestre en el json
        unset($admin->password);

        return $response->withStatus(201)
        ->withJson([
            'message' => 'User created successfully',
            'admin' => $admin
        ]);
    }
    public function update(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $id = $args['id'];
        if (!$this->validateData($data)) {
            return $response->withJson(['error' => 'Bad Request'], 400);
        }
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        /**
         * Validamos que el usuario exista
         */
        $user = User::find($id);
        if (!$user) {
            return $response->withJson(['error' => 'User not found'], 404);
        }
        /**
         * Validamos que el correo electrónico no esté en uso por otro usuario
         */
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser && $existingUser->id != $id) {
            return $response->withJson(['error' => 'Email already in use'], 400);
        }
        $user->update([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        //quitamos el password para que no se muestre en el json
        unset($user->password);
        return $response->withJson($user, 200);
    }
    public function delete(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        if (!$user) {
            return $response->withJson(['error' => 'User not found'], 404);
        }
        $user->delete();
        return $response->withJson(['message' => 'User deleted successfully'], 200);
    }
    public function index(Request $request, Response $response, $args)
    {
        $filter = $args['filter'];
        $search = $args['search']; 

        // Aplicar filtro y búsqueda a la consulta de usuarios
        $users = User::where(function ($query) use ($filter, $search) {
            //hacemos un swith para los tres casos
            if ($filter == 'name') {
                $query->where('name', 'like', "%$search%");
            } 
            if ($filter == 'last_name') {
                $query->where('last_name', 'like', "%$search%");
            } 
            if ($filter == 'email') {
                $query->where('email',  $search);
            }
        })->get();
        //quitamos el password para que no se muestre en el json
        foreach ($users as $user) {
            unset($user->password);
        }
        return $response->withJson($users, 200);
    }
}


