<?php
/**
 * API Endpoint: GET /api/items/read.php
 *
 * Fetch all items. Optionally filter by type.
 * Query params: ?type=lost|found (optional)
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
    $controller->readAll();
} catch (PDOException $e) {
    error_log('Item Read Endpoint Error: ' . $e->getMessage());
    require_once __DIR__ . '/../../utils/Response.php';
    Response::error('Internal server error.', 500);
}
