<?php

require_once 'Place.php';
require_once 'Food.php';

class Country {

    public int $id;
    public string $country;
    public string $region;
    public string $shortDescription;
    public string $fullDescription;
    public string $price;
    public string $capital;
    public string $visitDuration;
    public string $visitingTime;
    
    /** @var Place[] */
    public array $places; 
    
    /** @var Food[] */
    public array $foods;

    public string $countryPhoto;
    public string $mapPhoto;
    public string $creationDate;
    
    public array $reviews;
    public array $reviewersId;

    public function __construct( 
        int $id,
        string $country,
        string $region,
        string $shortDescription,
        string $fullDescription,
        string $price,
        string $capital,
        string $visitDuration,
        string $visitingTime,
        array $reviews,
        array $reviewersId,
        string $countryPhoto,
        string $mapPhoto,
        array $places,
        array $foods,
        string $creationDate
    ) {
        $this->id = $id;
        $this->country = $country;
        $this->region = $region;
        $this->shortDescription = $shortDescription;
        $this->fullDescription = $fullDescription;
        $this->price = $price;
        $this->capital = $capital;
        $this->visitDuration = $visitDuration;
        $this->visitingTime = $visitingTime;
        $this->reviews = $reviews;
        $this->reviewersId = $reviewersId;
        $this->countryPhoto = $countryPhoto;
        $this->mapPhoto = $mapPhoto;
        $this->places = $places;
        $this->foods = $foods;
        $this->creationDate = $creationDate;
    }

    public static function fromArray(array $data): self
    {
        
        $placesObjects = [];
        if (isset($data['places']) && is_array($data['places'])) {
            foreach ($data['places'] as $placeData) {
                $placesObjects[] = Place::fromArray($placeData);
            }
        }

        $foodsObjects = [];
        if (isset($data['foods']) && is_array($data['foods'])) {
            foreach ($data['foods'] as $foodData) {
                $foodsObjects[] = Food::fromArray($foodData);
            }
        }

        return new self(
            $data['id'],
            $data['country'],
            $data['region'],
            $data['short_description'],
            $data['full_description'],
            $data['price'],
            $data['capital'],
            $data['visit_duration'],
            $data['visiting_time'],
            $data['reviews'] ?? [],
            $data['reviewers_id'] ?? [],
            $data['country_photo'],
            $data['map_photo'],
            $placesObjects,
            $foodsObjects,
            $data['creation_date']
        );
    }

    public function toArray(): array
    {
        $placesArray = array_map(fn($p) => $p->toArray(), $this->places);
        $foodsArray = array_map(fn($f) => $f->toArray(), $this->foods);

        return [
            'id' => $this->id,
            'country' => $this->country,
            'region' => $this->region,
            'short_description' => $this->shortDescription,
            'full_description' => $this->fullDescription,
            'price' => $this->price,
            'capital' => $this->capital,
            'visit_duration' => $this->visitDuration,
            'visiting_time' => $this->visitingTime,
            'reviews' => $this->reviews,
            'reviewers_id' => $this->reviewersId,
            'country_photo' => $this->countryPhoto,
            'map_photo' => $this->mapPhoto,
            'places' => $placesArray,
            'foods' => $foodsArray,
            'creation_date' => $this->creationDate
        ];
    }
}