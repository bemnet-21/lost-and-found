<?php
/**
 * API Endpoint: POST /api/logout.php
 *
 * Destroy the current user session and log out.
 */

// CORS & Headers
require_once dirname(__DIR__) . '/utils/cors.php';

// Enforce POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    require_once dirname(__DIR__) . '/utils/Response.php';
    Response::error('Method not allowed. Use POST.', 405);
}

// Session & Database
require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/config/db.php';

// Controller
require_once dirname(__DIR__) . '/controllers/AuthController.php';

try {
    $db = getDBConnection();
    $controller = new AuthController($db);
    $controller->logout();
} catch (PDOException $e) {
    error_log('Logout Endpoint Error: ' . $e->getMessage());
    require_once dirname(__DIR__) . '/utils/Response.php';
    Response::error('Internal server error.', 500);
}
