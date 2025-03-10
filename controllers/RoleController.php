<?php
require_once 'middlewares/AuthMiddleware.php';
require_once 'models/Role.php';

class RoleController
{
    private $role;

    public function __construct()
    {
        // Verify JWT token for all role endpoints
        $auth = new AuthMiddleware();
        $auth->handle();

        if ($GLOBALS['user']->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden: Admin access required']);
            exit;
        }

        $this->role = new Role();
    }

    public function index()
    {
        $roles = $this->role->getAll();
        echo json_encode(['data' => $roles]);
    }

    public function show($id)
    {
        $role = $this->role->find($id);

        if (!$role) {
            http_response_code(404);
            echo json_encode(['error' => 'Role not found']);
            return;
        }

        echo json_encode(['data' => $role]);
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Role name is required']);
            return;
        }

        $roleId = $this->role->create($data);

        if ($roleId) {
            $role = $this->role->find($roleId);
            http_response_code(201);
            echo json_encode(['data' => $role, 'message' => 'Role created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create role']);
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Role name is required']);
            return;
        }

        $role = $this->role->find($id);
        if (!$role) {
            http_response_code(404);
            echo json_encode(['error' => 'Role not found']);
            return;
        }

        if ($this->role->update($id, $data)) {
            $role = $this->role->find($id);
            echo json_encode(['data' => $role, 'message' => 'Role updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update role']);
        }
    }
}