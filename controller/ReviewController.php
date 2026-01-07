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
            if ($method === 'GET' && strpos($uri, '/reviews/pending') !== false) {
                $reviews = $this->reviewService->getPendingReviews($token);
                $this->sendResponse(200, $reviews);
                return;
            }

            if ($method === 'GET' && strpos($uri, '/reviews/my') !== false) {
                $reviews = $this->reviewService->getMyReviews($token);
                $this->sendResponse(200, $reviews);
                return;
            }

            if ($method === 'POST' && strpos($uri, '/reviews/moderate') !== false) {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['review_id']) || !isset($input['action'])) {
                    $this->sendResponse(400, ["error" => "Missing review_id or action"]);
                    return;
                }

                $res = $this->reviewService->moderateReview($token, $input['review_id'], $input['action']);
                $this->sendResponse(200, $res);
                return;
            }

            if ($method === 'GET') {
                if (isset($_GET['country_id'])) {
                    $reviews = $this->reviewService->getReviewsForCountry((int)$_GET['country_id']);
                    $this->sendResponse(200, $reviews);
                } else {
                    $this->sendResponse(400, ["error" => "Missing country_id"]);
                }
                return;
            }

            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!isset($input['country_id']) || !isset($input['text'])) {
                    $this->sendResponse(400, ["error" => "Missing country_id or text"]);
                    return;
                }

                $newReview = $this->reviewService->addReview($token, $input['country_id'], $input['text']);
                $this->sendResponse(201, $newReview);
                return;
            }

            if ($method === 'DELETE' && isset($_GET['id'])) {
                $success = $this->reviewService->deleteReview($token, (int)$_GET['id']);
                if ($success) {
                    $this->sendResponse(200, ["message" => "Review deleted"]);
                } else {
                    $this->sendResponse(404, ["error" => "Review not found or access denied"]);
                }
                return;
            }

        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            $this->sendResponse($code, ["error" => $e->getMessage()]);
            return;
        }

        $this->sendResponse(404, ["message" => "Route not found in ReviewController"]);
    }

    private function sendResponse($code, $data) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}