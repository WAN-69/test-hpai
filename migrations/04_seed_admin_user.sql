INSERT INTO users (name, email, password, role_id, created_at, updated_at)
VALUES ('Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
        (SELECT id FROM roles WHERE name = 'admin'), NOW(), NOW());