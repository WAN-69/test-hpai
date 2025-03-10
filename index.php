<?php
header('Content-Type: application/json');

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

if (preg_match('/^\/api\/users\/(\d+)$/', $uri, $matches)) {
    $userId = $matches[1];
    if ($requestMethod === 'GET') {
        require 'controllers/UserController.php';
        $controller = new UserController();
        $controller->show($userId);
    } elseif ($requestMethod === 'DELETE') {
        require 'controllers/UserController.php';
        $controller = new UserController();
        $controller->destroy($userId);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} elseif ($uri === '/api/users') {
    require 'controllers/UserController.php';
    $controller = new UserController();

    if ($requestMethod === 'GET') {
        $controller->index();
    } elseif ($requestMethod === 'POST') {
        $controller->store();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} elseif (preg_match('/^\/api\/roles\/(\d+)$/', $uri, $matches)) {
    $roleId = $matches[1];
    if ($requestMethod === 'GET') {
        require 'controllers/RoleController.php';
        $controller = new RoleController();
        $controller->show($roleId);
    } elseif ($requestMethod === 'PUT') {
        require 'controllers/RoleController.php';
        $controller = new RoleController();
        $controller->update($roleId);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} elseif ($uri === '/api/roles') {
    require 'controllers/RoleController.php';
    $controller = new RoleController();

    if ($requestMethod === 'GET') {
        $controller->index();
    } elseif ($requestMethod === 'POST') {
        $controller->store();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} elseif ($uri === '/api/login') {
    if ($requestMethod === 'POST') {
        require 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}