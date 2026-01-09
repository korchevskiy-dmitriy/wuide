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

    public function getReviewsForCountry(int $countryId, int $page = 1, int $limit = 5) {
        $allReviews = $this->reviewDao->findByCountryId($countryId);
        
        $approved = array_filter($allReviews, fn($r) => $r->status === 'approved');
        
        usort($approved, fn($a, $b) => $b->id - $a->id);

        $totalReviews = count($approved); 
        $totalPages = ceil($totalReviews / $limit); 
        $offset = ($page - 1) * $limit; 

        $slice = array_slice($approved, $offset, $limit);
        
        return [
            'reviews' => array_values(array_map(fn($r) => $r->toArray(), $slice)),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_reviews' => $totalReviews
            ]
        ];
    }

    public function addReview(string $token, int $countryId, string $text) {
        $user = $this->userDao->findByToken($token);
        if (!$user) throw new Exception("Unauthorized", 401);

        $text = trim($text);
        if (empty($text)) {
            throw new Exception("Review text cannot be empty", 400);
        }
        if (strlen($text) > 500) {
            throw new Exception("Review is too long (max 500 chars)", 400);
        }

        $safeText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        $country = $this->countryDao->findById($countryId);
        if (!$country) throw new Exception("Country not found", 404);

        $review = new Review(
            0, 
            $user['id'], 
            $user['name'], 
            $user['photo_url'] ?? null, 
            $country->id, 
            $country->country, 
            $safeText, 
            date('Y-m-d'),
            'pending'
        );

        $savedReview = $this->reviewDao->save($review);
        return $savedReview->toArray();
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
            $this->reviewDao->update($review);
            return ["message" => "Review approved and published"];
        } elseif ($action === 'reject') {
            $this->reviewDao->delete($reviewId);
            return ["message" => "Review rejected and deleted from database"];
        } else {
            throw new Exception("Invalid action. Use 'approve' or 'reject'", 400);
        }
    }

    private function checkAdmin(string $token) {
        $user = $this->userDao->findByToken($token);
        if (!$user || ($user['role'] ?? 'user') !== 'admin') {
            throw new Exception("Access denied. Admins only.", 403);
        }
    }
}