<?php

require_once __DIR__ . '/../dao/ReviewDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/CountryDao.php';

class ReviewService {
    private ReviewDao $reviewDao;
    private UserDao $userDao;
    private CountryDao $countryDao;

    public function __construct() {
        $this->reviewDao = new ReviewDao();
        $this->userDao = new UserDao(); 
        $this->countryDao = new CountryDao(); 
    }

    public function getReviewsForCountry(int $countryId) {
        $reviews = $this->reviewDao->findByCountryId($countryId);
        return array_values(array_map(fn($r) => $r->toArray(), $reviews));
    }

    public function getUserReviews(string $token) {
        $user = $this->userDao->findByToken($token);
        if (!$user) throw new Exception("Unauthorized", 401);

        $reviews = $this->reviewDao->findByUserId($user['id']);
        return array_values(array_map(fn($r) => $r->toArray(), $reviews));
    }

    public function addReview(string $token, int $countryId, string $text) {
        $user = $this->userDao->findByToken($token);
        if (!$user) throw new Exception("Unauthorized", 401);

        $country = $this->countryDao->findById($countryId);
        if (!$country) throw new Exception("Country not found", 404);

        $review = new Review(
            0,
            $user['id'],
            $user['name'] . ' ' . $user['surname'],
            $user['photo_url'] ?? null,
            $country->id,
            $country->country,
            $text,
            date('Y-m-d')
        );

        return $this->reviewDao->save($review)->toArray();
    }

    public function deleteReview(string $token, int $reviewId) {
        $user = $this->userDao->findByToken($token);
        if (!$user) throw new Exception("Unauthorized", 401);

        $success = $this->reviewDao->delete($reviewId, $user['id']);
        if (!$success) {
            throw new Exception("Review not found or access denied", 403);
        }
        return ["message" => "Review deleted"];
    }
}