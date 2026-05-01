<?php
/**
 * API Endpoint: POST /api/items/create.php
 *
 * Create a new lost/found item listing.
 * Expects multipart/form-data with fields: title, description, category, type, location
 * Optional file: image
 * Requires authentication.
 */

// CORS & Headers
require_once __DIR__ . '/../../utils/cors.php';

// Enforce POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    require_once __DIR__ . '/../../utils/Response.php';
    Response::error('Method not allowed. Use POST.', 405);
}

// Session & Database
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';

// Controller
require_once __DIR__ . '/../../controllers/ItemController.php';

try {
    $db = getDBConnection();
    $controller = new ItemController($db);
    $controller->create();
} catch (PDOException $e) {
    error_log('Item Create Endpoint Error: ' . $e->getMessage());
    require_once __DIR__ . '/../../utils/Response.php';
    Response::error('Internal server error.', 500);
}

// $maxSize = 5000000;
