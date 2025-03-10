<?php
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$migrations_dir = 'migrations/';
$files = scandir($migrations_dir);
sort($files); // Ensure files are executed in order

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
        echo "Running migration: $file\n";
        $sql = file_get_contents($migrations_dir . $file);

        try {
            $pdo->exec($sql);
            echo "Migration $file completed successfully\n";
        } catch (PDOException $e) {
            die("Error in migration $file: " . $e->getMessage());
        }
    }
}

echo "All migrations completed\n";
