<?php
/**
 * API Endpoint: GET /api/items/search.php?q=keyword
 *
 * Search items by keyword in title, description, or category.
 * Query params: ?q=keyword (required, min 2 chars)
 */

// CORS & Headers
require_once __DIR__ . '/../../utils/cors.php';

// Enforce GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    require_once __DIR__ . '/../../utils/Response.php';
    Response::error('Method not allowed. Use GET.', 405);
}

// Session & Database
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';

// Controller
require_once __DIR__ . '/../../controllers/ItemController.php';

try {
    $db = getDBConnection();
    $controller = new ItemController($db);
    $controller->search();
} catch (PDOException $e) {
    error_log('Item Search Endpoint Error: ' . $e->getMessage());
    require_once __DIR__ . '/../../utils/Response.php';
    Response::error('Internal server error.', 500);
}
