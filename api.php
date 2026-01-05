<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/controller/AuthController.php';
require_once __DIR__ . '/controller/CountryController.php';
require_once __DIR__ . '/controller/ReviewController.php';

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$uriPath = parse_url($requestUri, PHP_URL_PATH);

if (strpos($uriPath, '/auth') !== false) {
    $controller = new AuthController();
    $controller->handleRequest($method, $uriPath);
} 
elseif (strpos($uriPath, '/countries') !== false) {
    $controller = new CountryController();
    $controller->handleRequest($method, $uriPath);
}
elseif (strpos($uriPath, '/reviews') !== false) {
    $controller = new ReviewController();
    $controller->handleRequest($method, $uriPath);
} 
elseif (strpos($uriPath, '/test') !== false) {
    echo json_encode(["status" => "success", "message" => "Mac server is working!"]);
}
else {
    http_response_code(404);
    echo json_encode(["error" => "Route not found", "path" => $uriPath]);
}