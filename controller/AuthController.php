<?php

require_once __DIR__ . '/../service/UserService.php';
require_once __DIR__ . '/../dto/UserRegisterDto.php';
require_once __DIR__ . '/../dto/UserLoginDto.php';

class AuthController {
    private $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    public function handleRequest($method, $uri) {
        $inputData = json_decode(file_get_contents("php://input"), true);
        $authHeader = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? '';
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            if ($method === 'POST' && strpos($uri, '/register') !== false) {
                $dto = new UserRegisterDto($inputData);
                $this->sendResponse(201, $this->userService->register($dto));
                return;
            } 

            if ($method === 'POST' && strpos($uri, '/login') !== false) {
                $dto = new UserLoginDto($inputData);
                $this->sendResponse(200, $this->userService->login($dto));
                return;
            }
            
            $currentUser = $this->userService->authenticate($token);

            if ($method === 'GET' && strpos($uri, '/profile') !== false) {
                unset($currentUser['password']);
                unset($currentUser['api_token']);
                $this->sendResponse(200, ["user" => $currentUser]);
                return;
            }

            if ($method === 'PUT' && strpos($uri, '/profile') !== false) {
                $updatedUser = $this->userService->updateProfile($token, $inputData);
                
                $this->sendResponse(200, [
                    "message" => "Profile updated successfully",
                    "user" => $updatedUser
                ]);
                return;
            }

            if ($method === 'PUT' && preg_match('/\/users\/(\d+)/', $uri, $matches)) {
                $targetId = $matches[1];
                $this->userService->authorize($currentUser, $targetId);
                $this->sendResponse(200, ["message" => "Data updated successfully"]);
                return;
            }

            $this->sendResponse(404, ["message" => "Path not found"]);

        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            $this->sendResponse($code, ["error" => $e->getMessage()]);
        }
    }

    private function sendResponse($code, $data) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}