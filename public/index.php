<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../vendor/autoload.php';

use App\UserController;
use App\AuthController;

$config = require '../src/config.php';

$database = new App\Database($config);
$db = $database->connect();

header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($uri == '/secure-api/public/api/register' && $requestMethod == 'POST') {
    $controller = new AuthController($db, $config['jwt_secret']);
    $controller->register();
} elseif ($uri == '/secure-api/public/api/login' && $requestMethod == 'POST') {
    $controller = new AuthController($db, $config['jwt_secret']);
    $controller->login();
} elseif ($uri == '/secure-api/public/api/getUser' && $requestMethod == 'GET') {
    $controller = new UserController($db, $config['jwt_secret']);
    $controller->getUser();
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["message" => "Endpoint not found"]);
}


