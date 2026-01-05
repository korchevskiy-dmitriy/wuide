<?php

require_once __DIR__ . '/../entity/Country.php';

class CountryDao {
    private $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../country.json'; 
    }

    public function getAll(): array {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $jsonContent = file_get_contents($this->filePath);
        $dataArray = json_decode($jsonContent, true);

        if (!$dataArray) {
            return [];
        }

        $countries = [];
        foreach ($dataArray as $item) {
            $countries[] = Country::fromArray($item);
        }

        return $countries;
    }

    public function findById(int $id): ?Country {
        $countries = $this->getAll();
        
        foreach ($countries as $country) {
            if ($country->id === $id) {
                return $country;
            }
        }
        
        return null;
    }
}