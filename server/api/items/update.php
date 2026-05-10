<?php
/**
 * API Endpoint: PUT /api/items/update.php
 *
 * Update the status of an item (e.g., mark as "resolved").
 * Expects JSON body: { "id": int, "status": "active"|"resolved" }
 * Requires authentication. Only the item owner can update.
 */

// CORS & Headers
require_once dirname(__DIR__, 2) . '/utils/cors.php';

// Enforce PUT method
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    require_once dirname(__DIR__, 2) . '/utils/Response.php';
    Response::error('Method not allowed. Use PUT.', 405);
}

// Session & Database
require_once dirname(__DIR__, 2) . '/config/session.php';
require_once dirname(__DIR__, 2) . '/config/db.php';

// Controller
require_once dirname(__DIR__, 2) . '/controllers/ItemController.php';

try {
    $db = getDBConnection();
    $controller = new ItemController($db);
    $controller->update();
} catch (PDOException $e) {
    error_log('Item Update Endpoint Error: ' . $e->getMessage());
    require_once dirname(__DIR__, 2) . '/utils/Response.php';
    Response::error('Internal server error.', 500);
}
