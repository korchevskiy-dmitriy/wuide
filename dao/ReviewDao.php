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
        return array_values(array_filter($all, fn($r) => $r->countryId === $countryId));
    }

    public function findById(int $id): ?Review {
        $all = $this->getAll();
        foreach ($all as $review) {
            if ($review->id === $id) {
                return $review;
            }
        }
        return null;
    }

    public function findByUserId(int $userId): array {
        $all = $this->getAll();
        return array_values(array_filter($all, fn($r) => $r->userId === $userId));
    }

    public function save(Review $review) {
        $all = $this->getAll();
        if ($review->id === 0) {
            $maxId = 0;
            foreach ($all as $r) {
                if ($r->id > $maxId) $maxId = $r->id;
            }
            $review->id = $maxId + 1;
        }
        $all[] = $review;
        $this->saveAll($all);
        return $review;
    }

    public function update(Review $updatedReview) {
        $all = $this->getAll();
        $newList = [];
        foreach ($all as $review) {
            if ($review->id === $updatedReview->id) {
                $newList[] = $updatedReview;
            } else {
                $newList[] = $review;
            }
        }
        $this->saveAll($newList);
    }

    public function delete(int $id) {
        $all = $this->getAll();
        $newList = array_filter($all, fn($r) => $r->id !== $id);
        $this->saveAll(array_values($newList));
    }

    private function saveAll(array $reviews) {
        $data = array_map(fn($r) => $r->toArray(), $reviews);
        file_put_contents(
            $this->filePath, 
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}