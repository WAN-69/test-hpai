<?php
require_once 'Database.php';
require_once 'Role.php';

class User
{
    private $db;
    private $role;

    public function __construct()
    {
        $this->db = new Database();
        $this->role = new Role();
    }

    public function getAll()
    {
        $query = "SELECT u.id, u.name, u.email, r.name as role, u.created_at, u.updated_at 
                  FROM users u
                  JOIN roles r ON u.role_id = r.id
                  WHERE u.deleted_at IS NULL";
        return $this->db->query($query);
    }

    public function find($id)
    {
        $query = "SELECT u.id, u.name, u.email, r.name as role, u.created_at, u.updated_at 
                  FROM users u
                  JOIN roles r ON u.role_id = r.id
                  WHERE u.id = ? AND u.deleted_at IS NULL";
        $result = $this->db->query($query, [$id]);

        return $result ? $result[0] : null;
    }

    public function findByEmail($email)
    {
        $query = "SELECT u.id, u.name, u.email, u.password, r.name as role, u.created_at, u.updated_at 
                  FROM users u
                  JOIN roles r ON u.role_id = r.id
                  WHERE u.email = ? AND u.deleted_at IS NULL";
        $result = $this->db->query($query, [$email]);

        return $result ? $result[0] : null;
    }

    public function create($data)
    {
        $role = isset($data['role']) ? $this->role->findByName($data['role']) : null;

        if (!$role) {
            return false;
        }

        $query = "INSERT INTO users (name, email, password, role_id, created_at, updated_at) 
                  VALUES (?, ?, ?, ?, NOW(), NOW())";
        return $this->db->execute($query, [
            $data['name'],
            $data['email'],
            $data['password'],
            $role['id']
        ]);
    }

    public function update($id, $data)
    {
        $setFields = [];
        $params = [];

        if (isset($data['name'])) {
            $setFields[] = "name = ?";
            $params[] = $data['name'];
        }

        if (isset($data['email'])) {
            $setFields[] = "email = ?";
            $params[] = $data['email'];
        }

        if (isset($data['password'])) {
            $setFields[] = "password = ?";
            $params[] = $data['password'];
        }

        if (isset($data['role'])) {
            $role = $this->role->findByName($data['role']);
            if ($role) {
                $setFields[] = "role_id = ?";
                $params[] = $role['id'];
            }
        }

        if (empty($setFields)) {
            return false;
        }

        $setFields[] = "updated_at = NOW()";
        $params[] = $id;

        $query = "UPDATE users SET " . implode(", ", $setFields) . " WHERE id = ?";
        return $this->db->execute($query, $params);
    }

    public function softDelete($id)
    {
        $query = "UPDATE users SET deleted_at = NOW() WHERE id = ?";
        return $this->db->execute($query, [$id]);
    }
}