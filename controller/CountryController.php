<?php
require_once __DIR__ . '/../service/CountryService.php';

class CountryController {
    private $countryService;

    public function __construct() {
        $this->countryService = new CountryService();
    }

    public function handleRequest($method, $uri) {
        header('Content-Type: application/json');

        try {
            if ($method === 'GET' && (preg_match('/\/countries\/?$/', $uri))) {
                $countries = $this->countryService->getAllCountries();
                echo json_encode($countries);
                return;
            }
            if ($method === 'GET' && preg_match('/\/countries\/(\d+)/', $uri, $matches)) {
                $id = $matches[1]; 
                $country = $this->countryService->getCountryById($id);
                
                if ($country) {
                    echo json_encode($country);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Country not found"]);
                }
                return;
            }

            http_response_code(404);
            echo json_encode(["message" => "Route not found"]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}