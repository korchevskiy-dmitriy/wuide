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
    
    public function findById(int $id): ?Review {
        $all = $this->getAll();
        foreach ($all as $r) {
            if ($r->id === $id) return $r;
        }
        return null;
    }

    public function save(Review $review) {
        $reviews = $this->getAll();
        $maxId = 0;
        foreach ($reviews as $r) {
            if ($r->id > $maxId) $maxId = $r->id;
        }
        $review->id = $maxId + 1;
        
        $reviews[] = $review;
        $this->saveAll($reviews);
        return $review;
    }

    public function update(Review $updatedReview) {
        $reviews = $this->getAll();
        $found = false;
        foreach ($reviews as $key => $r) {
            if ($r->id === $updatedReview->id) {
                $reviews[$key] = $updatedReview;
                $found = true;
                break;
            }
        }
        if ($found) {
            $this->saveAll($reviews);
        }
        return $found;
    }

    public function delete(int $reviewId) {
        $reviews = $this->getAll();
        $filtered = array_filter($reviews, fn($r) => $r->id !== $reviewId);
        
        if (count($filtered) < count($reviews)) {
            $this->saveAll(array_values($filtered));
            return true;
        }
        return false;
    }

    private function saveAll(array $reviews) {
        $data = array_map(fn($r) => $r->toArray(), $reviews);
        file_put_contents(
            $this->filePath, 
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }
}