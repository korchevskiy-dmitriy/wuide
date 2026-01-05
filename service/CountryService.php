<?php

class CountryService {
    private $jsonFile;

    public function __construct() {
        $this->jsonFile = __DIR__ . '/../country.json';
    }

    public function getAllCountries() {
        if (!file_exists($this->jsonFile)) {
            return [];
        }
        
        $jsonContent = file_get_contents($this->jsonFile);
        return json_decode($jsonContent, true) ?? [];
    }

    public function getCountryById($id) {
        $countries = $this->getAllCountries();
        
        foreach ($countries as $country) {
            if ($country['id'] == $id) {
                return $country;
            }
        }
        return null; 
    }
}