<?php

require_once __DIR__ . '/../entity/Review.php';

class ReviewDao {
    private $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../reviews.json';
    }

    public function getAll(): array {
        if (!file_exists($this->filePath)) return [];
        $json = file_get_contents($this->filePath);
        $data = json_decode($json, true) ?? [];
        return array_map(fn($item) => Review::fromArray($item), $data);
    }

    public function findByCountryId(int $countryId): array {
        $all = $this->getAll();
        return array_values(array_filter($all, fn($r) => $r->countryId == $countryId));
    }

    public function findByUserId(int $userId): array {
        $all = $this->getAll();
        return array_values(array_filter($all, fn($r) => $r->userId == $userId));
    }

    public function save(Review $review) {
        $reviews = $this->getAll();
        $newId = count($reviews) > 0 ? end($reviews)->id + 1 : 1;
        $review->id = $newId;
        
        $reviews[] = $review;
        $this->saveAll($reviews);
        return $review;
    }

    public function delete(int $reviewId, int $userId) {
        $reviews = $this->getAll();

        $countBefore = count($reviews);

        $filtered = array_filter($reviews, function($review) use ($reviewId, $userId) {
            $rId = (int)$review->id;
            $uId = (int)$review->userId;

            if ($rId === $reviewId && $uId === $userId) {
                return false; 
            }
            return true;
        });

        $countAfter = count($filtered);

        if ($countAfter < $countBefore) {
            $this->saveAll(array_values($filtered));
            return true;
        }

        return false;
    }

    private function saveAll(array $reviews) {
        $data = array_map(fn($r) => $r->toArray(), $reviews);
        file_put_contents(
            $this->filePath, 
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
    
}