<?php
require_once 'Database.php';

class Role
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        $query = "SELECT id, name, created_at, updated_at FROM roles";
        return $this->db->query($query);
    }

    public function find($id)
    {
        $query = "SELECT id, name, created_at, updated_at FROM roles WHERE id = ?";
        $result = $this->db->query($query, [$id]);

        return $result ? $result[0] : null;
    }

    public function findByName($name)
    {
        $query = "SELECT id, name, created_at, updated_at FROM roles WHERE name = ?";
        $result = $this->db->query($query, [$name]);

        return $result ? $result[0] : null;
    }

    public function create($data)
    {
        $query = "INSERT INTO roles (name, created_at, updated_at) VALUES (?, NOW(), NOW())";
        return $this->db->execute($query, [$data['name']]);
    }

    public function update($id, $data)
    {
        $query = "UPDATE roles SET name = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($query, [$data['name'], $id]);
    }
}