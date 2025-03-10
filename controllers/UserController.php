<?php
require_once 'middlewares/AuthMiddleware.php';
require_once 'models/User.php';

class UserController
{
    private $user;

    public function __construct()
    {
        $auth = new AuthMiddleware();
        $auth->handle();

        $this->user = new User();
    }

    public function index()
    {
        $users = $this->user->getAll();
        echo json_encode(['data' => $users]);
    }

    public function show($id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }

        echo json_encode(['data' => $user]);
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['role'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        if (!in_array($data['role'], ['admin', 'user'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Role must be admin or user']);
            return;
        }

        if ($this->user->findByEmail($data['email'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Email already exists']);
            return;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $userId = $this->user->create($data);

        if ($userId) {
            $user = $this->user->find($userId);
            http_response_code(201);
            echo json_encode(['data' => $user, 'message' => 'User created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create user']);
        }
    }

    public function destroy($id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }

        if ($this->user->softDelete($id)) {
            echo json_encode(['message' => 'User deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete user']);
        }
    }
}