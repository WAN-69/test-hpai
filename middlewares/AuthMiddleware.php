<?php
require_once 'helpers/JwtHelper.php';

class AuthMiddleware
{
    public function handle()
    {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

        if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized: Token required']);
            exit;
        }

        $token = $matches[1];
        $payload = JwtHelper::validateToken($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized: Invalid token']);
            exit;
        }

        $GLOBALS['user'] = $payload;
    }
}