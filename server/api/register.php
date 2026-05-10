<?php
/**
 * API Endpoint: POST /api/register.php
 *
 * Register a new user account.
 * Expects JSON body: { "username": string, "email": string, "password": string }
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
    $controller->register();
} catch (PDOException $e) {
    error_log('Register Endpoint Error: ' . $e->getMessage());
    require_once dirname(__DIR__) . '/utils/Response.php';
    Response::error('Internal server error.', 500);
}
