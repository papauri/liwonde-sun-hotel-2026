<?php
/**
 * Review Responses API
 * Liwonde Sun Hotel - Admin API for managing admin responses to reviews
 * 
 * Endpoints:
 * - GET: Fetch responses for a specific review
 * - POST: Add a new admin response to a review
 */

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set JSON response header
header('Content-Type: application/json');

// Include database configuration
require_once __DIR__ . '/../../config/database.php';

// Start session for admin authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to send JSON response
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Helper function to send error response
function sendError($message, $statusCode = 400, $details = null) {
    $response = [
        'success' => false,
        'message' => $message
    ];
    if ($details !== null) {
        $response['details'] = $details;
    }
    sendResponse($response, $statusCode);
}

// Helper function to validate response data
function validateResponseData($data) {
    $errors = [];
    
    // Required fields
    $required_fields = ['review_id', 'response'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    // Validate review_id
    if (isset($data['review_id'])) {
        if (!is_numeric($data['review_id']) || (int)$data['review_id'] < 1) {
            $errors['review_id'] = 'Invalid review ID';
        }
    }
    
    // Validate admin_id if provided
    if (isset($data['admin_id']) && $data['admin_id'] !== null && $data['admin_id'] !== '') {
        if (!is_numeric($data['admin_id']) || (int)$data['admin_id'] < 1) {
            $errors['admin_id'] = 'Invalid admin ID';
        }
    }
    
    // Validate response length
    if (isset($data['response'])) {
        $response_length = strlen(trim($data['response']));
        if ($response_length < 10) {
            $errors['response'] = 'Response must be at least 10 characters long';
        }
        if ($response_length > 5000) {
            $errors['response'] = 'Response must not exceed 5000 characters';
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Require admin authentication for all operations
if (!isset($_SESSION['admin_user'])) {
    sendError('Authentication required', 401);
}

// Parse request body for POST requests
$input = [];
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    if (!empty($rawInput)) {
        $input = json_decode($rawInput, true) ?? [];
    }
    // Also merge with $_POST for form data
    if (!empty($_POST)) {
        $input = array_merge($input, $_POST);
    }
}

try {
    switch ($method) {
        case 'GET':
            // Fetch responses for a specific review
            if (!isset($_GET['review_id'])) {
                sendError('review_id parameter is required', 400);
            }
            
            $review_id = (int)$_GET['review_id'];
            
            // Validate review exists
            $stmt = $pdo->prepare("SELECT id, guest_name, title FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            $review = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$review) {
                sendError('Review not found', 404);
            }
            
            // Fetch responses with admin details
            $sql = "
                SELECT 
                    rr.*,
                    au.username as admin_username,
                    au.email as admin_email
                FROM review_responses rr
                LEFT JOIN admin_users au ON rr.admin_id = au.id
                WHERE rr.review_id = ?
                ORDER BY rr.created_at ASC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$review_id]);
            $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            sendResponse([
                'success' => true,
                'data' => [
                    'review' => $review,
                    'responses' => $responses
                ]
            ]);
            break;
            
        case 'POST':
            // Add a new admin response to a review
            $validation = validateResponseData($input);
            if (!$validation['valid']) {
                sendError('Validation failed', 400, $validation['errors']);
            }
            
            $review_id = (int)$input['review_id'];
            $response = trim($input['response']);
            // Default admin_id to logged-in admin, but allow override via input
            $admin_id = isset($input['admin_id']) && $input['admin_id'] !== '' ? (int)$input['admin_id'] : null;
            if ($admin_id === null && isset($_SESSION['admin_user']['id'])) {
                $admin_id = (int)$_SESSION['admin_user']['id'];
            }
            
            // Validate review exists
            $stmt = $pdo->prepare("SELECT id, status FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            $review = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$review) {
                sendError('Review not found', 404);
            }
            
            // Validate admin_id if provided
            if ($admin_id !== null) {
                $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE id = ?");
                $stmt->execute([$admin_id]);
                if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                    sendError('Admin user not found', 404);
                }
            }
            
            // Insert response
            $sql = "
                INSERT INTO review_responses (review_id, admin_id, response)
                VALUES (?, ?, ?)
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$review_id, $admin_id, $response]);
            
            $response_id = $pdo->lastInsertId();
            
            // Fetch the created response with admin details
            $sql = "
                SELECT 
                    rr.*,
                    au.username as admin_username,
                    au.email as admin_email
                FROM review_responses rr
                LEFT JOIN admin_users au ON rr.admin_id = au.id
                WHERE rr.id = ?
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$response_id]);
            $new_response = $stmt->fetch(PDO::FETCH_ASSOC);
            
            sendResponse([
                'success' => true,
                'message' => 'Response added successfully',
                'data' => $new_response
            ], 201);
            break;
            
        default:
            sendError('Method not allowed', 405);
            break;
    }
    
} catch (PDOException $e) {
    error_log("Database error in review-responses.php: " . $e->getMessage());
    sendError('Database error occurred', 500, $e->getMessage());
} catch (Exception $e) {
    error_log("Error in review-responses.php: " . $e->getMessage());
    sendError('An error occurred', 500, $e->getMessage());
}
