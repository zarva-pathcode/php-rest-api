<?php

namespace App;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

class UserController
{
    private $conn;
    private $jwt_secret;

    public function __construct($db, $jwt_secret)
    {
        $this->conn = $db;
        $this->jwt_secret = $jwt_secret;
    }

    public function getUser()
    {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode(["message" => "Access denied"]);
            return;
        }

        $jwt = $matches[1];

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwt_secret, 'HS256'));

            $query = "SELECT id, email FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $decoded->id);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($user);
        } catch (\Exception $e) {
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode(["message" => "Access denied"]);
        }
    }
}
