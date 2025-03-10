<?php
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $token;
    private $baseUrl = 'http://app:80';

    public function setUp(): void
    {
        $ch = curl_init($this->baseUrl . '/api/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'email' => 'admin@example.com',
            'password' => 'password'
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        $this->token = $data['token'];
    }

    public function testGetAllUsers()
    {
        $ch = curl_init($this->baseUrl . '/api/users');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode);

        $data = json_decode($response, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);

        // Check that role is a string, not an ID
        if (count($data['data']) > 0) {
            $this->assertIsString($data['data'][0]['role']);
        }
    }

    public function testCreateUser()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'role' => 'user'
        ];

        $ch = curl_init($this->baseUrl . '/api/users');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(201, $httpCode);

        $data = json_decode($response, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals($userData['name'], $data['data']['name']);
        $this->assertEquals($userData['email'], $data['data']['email']);
        $this->assertEquals($userData['role'], $data['data']['role']);

        return $data['data']['id'];
    }

    /**
     * @depends testCreateUser
     */
    public function testGetUser($userId)
    {
        $ch = curl_init($this->baseUrl . '/api/users/' . $userId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode);

        $data = json_decode($response, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals($userId, $data['data']['id']);
        $this->assertIsString($data['data']['role']);

        return $userId;
    }

    /**
     * @depends testGetUser
     */
    public function testDeleteUser($userId)
    {
        $ch = curl_init($this->baseUrl . '/api/users/' . $userId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode);

        $data = json_decode($response, true);
        $this->assertArrayHasKey('message', $data);
    }
}