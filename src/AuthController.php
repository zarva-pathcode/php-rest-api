<?php

namespace App;

use Firebase\JWT\JWT;
use PDO;

class AuthController
{
    private $conn;
    private $jwt_secret;

    public function __construct($db, $jwt_secret)
    {
        $this->conn = $db;
        $this->jwt_secret = $jwt_secret;
    }

    public function register()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->email) || !isset($data->password)) {
            echo json_encode(["message" => "Invalid data"]);
            return;
        }

        $hashedPassword = password_hash($data->password, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $data->email);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            echo json_encode(["message" => "User registered"]);
        } else {
            echo json_encode(["message" => "Failed to register user"]);
        }
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"));

        $query = "SELECT id, email, password FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $data->email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($data->password, $user['password'])) {
            $token = JWT::encode(['id' => $user['id']], $this->jwt_secret, 'HS256');
            echo json_encode(["token" => $token]);
        } else {
            echo json_encode(["message" => "Invalid credentials"]);
        }
    }
}
