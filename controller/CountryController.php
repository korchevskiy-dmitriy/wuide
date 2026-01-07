<?php

require_once __DIR__ . '/../service/CountryService.php';

class CountryController {
    private CountryService $countryService;

    public function __construct() {
        $this->countryService = new CountryService();
    }

    public function handleRequest($method, $uri) {
        if ($method === 'GET') {
            try {
                if (isset($_GET['id'])) {
                    $id = (int)$_GET['id'];
                    $country = $this->countryService->getCountryById($id);
                    $this->sendResponse(200, $country->toArray());
                } else {
                    $region = $_GET['region'] ?? null;
                    $price = $_GET['price'] ?? null;
                    $countries = $this->countryService->getFilteredCountries($region, $price);
                    $this->sendResponse(200, $countries);
                }
            } catch (Exception $e) {
                $code = $e->getCode() ?: 500;
                $this->sendResponse($code, ["error" => $e->getMessage()]);
            }
            return;
        }

        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                $newCountry = $this->countryService->createCountry($token, $input);
                
                $this->sendResponse(201, $newCountry);
                return;
            }

            if ($method === 'PUT' && isset($_GET['id'])) {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = (int)$_GET['id'];

                $updatedCountry = $this->countryService->updateCountry($token, $id, $input);
                
                $this->sendResponse(200, $updatedCountry);
                return;
            }

        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            $this->sendResponse($code, ["error" => $e->getMessage()]);
            return;
        }

        $this->sendResponse(405, ["error" => "Method not allowed"]);
    }

    private function sendResponse($code, $data) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}