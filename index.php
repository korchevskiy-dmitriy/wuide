<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'controller/AuthController.php';
require_once 'controller/CountryController.php';
require_once 'controller/ReviewController.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (strpos($uri, '/countries') !== false) {
    $controller = new CountryController();
    $controller->handleRequest($method, $uri);
} 
elseif (strpos($uri, '/reviews') !== false) {
    $controller = new ReviewController();
    $controller->handleRequest($method, $uri);
} 
else {
    $controller = new AuthController();
    $controller->handleRequest($method, $uri);
}