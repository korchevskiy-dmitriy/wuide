<?php

require_once __DIR__ . '/../dao/CountryDao.php';
require_once __DIR__ . '/../dao/UserDao.php'; //

class CountryService {
    private CountryDao $countryDao;

    public function __construct() {
        $this->countryDao = new CountryDao();
    }

    public function getFilteredCountries(?string $region, ?string $targetPrice): array {
        $allCountries = $this->countryDao->getAll();

        usort($allCountries, fn($a, $b) => $b->id <=> $a->id);

        if (!$region && !$targetPrice) {
            return array_map(fn($c) => $c->toArray(), $allCountries);
        }

        $filtered = [];

        foreach ($allCountries as $country) {
            $matchesRegion = true;
            $matchesPrice = true;

            if ($region) {
                if (strcasecmp($country->region, trim($region)) !== 0) {
                    $matchesRegion = false;
                }
            }

            if ($targetPrice) {
                if ($country->price !== $targetPrice) {
                    $matchesPrice = false;
                }
            }

            if ($matchesRegion && $matchesPrice) {
                $filtered[] = $country->toArray();
            }
        }

        return $filtered;
    }

    public function getCountryById(int $id): Country {
        $country = $this->countryDao->findById($id);
        
        if (!$country) {
            throw new Exception("Country with ID $id not found", 404);
        }

        return $country;
    }

    public function createCountry(string $token, array $data) {
        $this->checkAdmin($token);

        $all = $this->countryDao->getAll();
        
        $maxId = 0;
        foreach ($all as $c) {
            if ($c->id > $maxId) {
                $maxId = $c->id;
            }
        }
        $newId = $maxId + 1;
        
        $data['id'] = $newId;
        $data['creation_date'] = date('Y-m-d');
        
        $country = Country::fromArray($data);
        
        $this->countryDao->save($country);
        return $country->toArray();
    }

    public function updateCountry(string $token, int $id, array $data) {
        $this->checkAdmin($token);

        $existing = $this->countryDao->findById($id);
        if (!$existing) throw new Exception("Country not found", 404);

        $mergedData = array_merge($existing->toArray(), $data);
        
        $mergedData['id'] = $id;
        $mergedData['creation_date'] = $existing->creationDate;

        $updatedCountry = Country::fromArray($mergedData);

        $this->countryDao->update($updatedCountry);
        return $updatedCountry->toArray();
    }

    private function checkAdmin(string $token) {
        $userDao = new UserDao(); 
        $user = $userDao->findByToken($token);
        if (!$user || ($user['role'] ?? 'user') !== 'admin') {
            throw new Exception("Access denied. Admins only.", 403);
        }
    }
}