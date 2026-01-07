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
        $approved = array_filter($reviews, fn($r) => $r->status === 'approved');
        return array_values(array_map(fn($r) => $r->toArray(), $approved));
    }

    public function addReview(string $token, int $countryId, string $text) {
        $user = $this->userDao->findByToken($token);
        if (!$user) throw new Exception("Unauthorized", 401);

        $country = $this->countryDao->findById($countryId);
        if (!$country) throw new Exception("Country not found", 404);

        $review = new Review(
            0, 
            $user['id'], 
            $user['name'], 
            $user['photo_url'] ?? null, 
            $country->id, 
            $country->country, 
            $text, 
            date('Y-m-d'),
            'pending'
        );

        return $this->reviewDao->save($review)->toArray();
    }

    public function getMyReviews(string $token) {
        $user = $this->userDao->findByToken($token);
        if (!$user) throw new Exception("Unauthorized", 401);

        $reviews = $this->reviewDao->findByUserId($user['id']);
        
        return array_map(fn($r) => $r->toArray(), $reviews);
    }

    public function deleteReview(string $token, int $reviewId) {
        $user = $this->userDao->findByToken($token);
        if (!$user) throw new Exception("Unauthorized", 401);

        $review = $this->reviewDao->findById($reviewId);
        if (!$review) throw new Exception("Review not found", 404);

        if ($review->userId !== $user['id'] && ($user['role'] ?? 'user') !== 'admin') {
            throw new Exception("Access denied", 403);
        }

        $this->reviewDao->delete($reviewId);
        return true;
    }

    public function getPendingReviews(string $token) {
        $this->checkAdmin($token);
        $all = $this->reviewDao->getAll();
        $pending = array_filter($all, fn($r) => $r->status === 'pending');
        return array_values(array_map(fn($r) => $r->toArray(), $pending));
    }

    public function moderateReview(string $token, int $reviewId, string $action) {
        $this->checkAdmin($token);

        $review = $this->reviewDao->findById($reviewId);
        if (!$review) throw new Exception("Review not found", 404);

        if ($action === 'approve') {
            $review->status = 'approved';
        } elseif ($action === 'reject') {
            $review->status = 'rejected';
        } else {
            throw new Exception("Invalid action", 400);
        }

        $this->reviewDao->update($review);
        return ["message" => "Review status updated to $action"];
    }

    private function checkAdmin(string $token) {
        $user = $this->userDao->findByToken($token);
        if (!$user || ($user['role'] ?? 'user') !== 'admin') {
            throw new Exception("Access denied. Admins only.", 403);
        }
    }
}