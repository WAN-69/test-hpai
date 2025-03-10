<?php
require_once 'models/User.php';
require_once 'helpers/JwtHelper.php';

class AuthController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        $user = $this->user->findByEmail($data['email']);

        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        if (!password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        $jwt = JwtHelper::generateToken([
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);

        echo json_encode([
            'token' => $jwt,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    }
}