<?php

require_once __DIR__ . '/../service/ReviewService.php';

class ReviewController {
    private ReviewService $reviewService;

    public function __construct() {
        $this->reviewService = new ReviewService();
    }

    public function handleRequest($method, $uri) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $authHeader);

        try {
            if ($method === 'GET' && isset($_GET['country_id'])) {
                $reviews = $this->reviewService->getReviewsForCountry((int)$_GET['country_id']);
                $this->sendResponse(200, $reviews);
                return;
            }

            if ($method === 'GET' && strpos($uri, '/reviews/my') !== false) {
                $reviews = $this->reviewService->getUserReviews($token);
                $this->sendResponse(200, $reviews);
                return;
            }

            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                if (empty($input['country_id']) || empty($input['text'])) {
                    throw new Exception("Missing country_id or text", 400);
                }
                
                $newReview = $this->reviewService->addReview($token, $input['country_id'], $input['text']);
                $this->sendResponse(201, $newReview);
                return;
            }

            if ($method === 'DELETE' && isset($_GET['id'])) {
                $result = $this->reviewService->deleteReview($token, (int)$_GET['id']);
                $this->sendResponse(200, $result);
                return;
            }

        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            $this->sendResponse($code, ["error" => $e->getMessage()]);
        }
    }

    private function sendResponse($code, $data) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}