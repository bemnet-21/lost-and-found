<?php
/**
 * API Endpoint: GET /api/check-auth.php
 *
 * Check if a valid session exists and return the authenticated user's data.
 * Crucial for React SPA refreshes to restore auth state.
 */

// CORS & Headers
require_once dirname(__DIR__) . '/utils/cors.php';

// Enforce GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    require_once dirname(__DIR__) . '/utils/Response.php';
    Response::error('Method not allowed. Use GET.', 405);
}

// Session & Database
require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/config/db.php';

// Controller
require_once dirname(__DIR__) . '/controllers/AuthController.php';

try {
    $db = getDBConnection();
    $controller = new AuthController($db);
    $controller->checkAuth();
} catch (PDOException $e) {
    error_log('Check Auth Endpoint Error: ' . $e->getMessage());
    require_once dirname(__DIR__) . '/utils/Response.php';
    Response::error('Internal server error.', 500);
}
