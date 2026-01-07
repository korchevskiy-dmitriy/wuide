<?php

require_once __DIR__ . '/../entity/Country.php';

class CountryDao {
    private $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../country.json';
    }

    public function getAll(): array {
        if (!file_exists($this->filePath)) return [];
        $json = file_get_contents($this->filePath);
        $data = json_decode($json, true) ?? [];
        
        return array_map(fn($item) => Country::fromArray($item), $data);
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

    public function save(Country $country) {
        $countries = $this->getAll();
        $countries[] = $country;
        $this->saveAll($countries);
    }

    public function update(Country $updatedCountry) {
        $countries = $this->getAll();
        $newReviewList = [];

        foreach ($countries as $c) {
            if ($c->id === $updatedCountry->id) {
                $newReviewList[] = $updatedCountry;
            } else {
                $newReviewList[] = $c;
            }
        }

        $this->saveAll($newReviewList);
    }

    private function saveAll(array $countries) {
        $data = array_map(fn($c) => $c->toArray(), $countries);
        
        file_put_contents(
            $this->filePath, 
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}