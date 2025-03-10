<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    public static function generateToken($payload)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // 1 hour expiration

        $data = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $payload
        ];

        return JWT::encode($data, $_ENV['JWT_SECRET'], 'HS256');
    }

    public static function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            return $decoded->data;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>