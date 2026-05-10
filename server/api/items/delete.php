<?php
/**
 * API Endpoint: DELETE /api/items/delete.php
 *
 * Delete an item listing.
 * Expects JSON body: { "id": int }
 * Requires authentication. Only the item owner can delete.
 */

// CORS & Headers
require_once dirname(__DIR__, 2) . '/utils/cors.php';

// Enforce DELETE method
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    require_once dirname(__DIR__, 2) . '/utils/Response.php';
    Response::error('Method not allowed. Use DELETE.', 405);
}

// Session & Database
require_once dirname(__DIR__, 2) . '/config/session.php';
require_once dirname(__DIR__, 2) . '/config/db.php';

// Controller
require_once dirname(__DIR__, 2) . '/controllers/ItemController.php';

try {
    $db = getDBConnection();
    $controller = new ItemController($db);
    $controller->deleteItem();
} catch (PDOException $e) {
    error_log('Item Delete Endpoint Error: ' . $e->getMessage());
    require_once dirname(__DIR__, 2) . '/utils/Response.php';
    Response::error('Internal server error.', 500);
}
