<?php
/**
 * API Endpoint: DELETE /api/items/delete.php
 *
 * Delete an item listing.
 * Expects JSON body: { "id": int }
 * Requires authentication. Only the item owner can delete.
 */

// CORS & Headers
require_once __DIR__ . '/../../utils/cors.php';

// Enforce DELETE method
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    require_once __DIR__ . '/../../utils/Response.php';
    Response::error('Method not allowed. Use DELETE.', 405);
}

// Session & Database
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';

// Controller
require_once __DIR__ . '/../../controllers/ItemController.php';

try {
    $db = getDBConnection();
    $controller = new ItemController($db);
    $controller->deleteItem();
} catch (PDOException $e) {
    error_log('Item Delete Endpoint Error: ' . $e->getMessage());
    require_once __DIR__ . '/../../utils/Response.php';
    Response::error('Internal server error.', 500);
}
