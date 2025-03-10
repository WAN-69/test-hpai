<?php
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
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

    public function testGetAllRoles()
    {
        $ch = curl_init($this->baseUrl . '/api/roles');
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
    }

    public function testCreateRole()
    {
        $roleName = 'manager' . time();
        $roleData = [
            'name' => $roleName
        ];

        $ch = curl_init($this->baseUrl . '/api/roles');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($roleData));
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
        $this->assertEquals($roleData['name'], $data['data']['name']);

        return $data['data']['id'];
    }

    /**
     * @depends testCreateRole
     */
    public function testGetRole($roleId)
    {
        $ch = curl_init($this->baseUrl . '/api/roles/' . $roleId);
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
        $this->assertEquals($roleId, $data['data']['id']);

        return $roleId;
    }

    /**
     * @depends testGetRole
     */
    public function testUpdateRole($roleId)
    {
        $roleData = [
            'name' => 'updated_role' . time()
        ];

        $ch = curl_init($this->baseUrl . '/api/roles/' . $roleId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($roleData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode);

        $data = json_decode($response, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals($roleData['name'], $data['data']['name']);
    }
}