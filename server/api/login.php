<?php
/**
 * API Endpoint: POST /api/login.php
 *
 * Authenticate an existing user and start a session.
 * Expects JSON body: { "email": string, "password": string }
 */

// CORS & Headers
require_once __DIR__ . '/../utils/cors.php';

// Enforce POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    require_once __DIR__ . '/../utils/Response.php';
    Response::error('Method not allowed. Use POST.', 405);
}

// Session & Database
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/db.php';

// Controller
require_once __DIR__ . '/../controllers/AuthController.php';

try {
    $db = getDBConnection();
    $controller = new AuthController($db);
    $controller->login();
} catch (PDOException $e) {
    error_log('Login Endpoint Error: ' . $e->getMessage());
    require_once __DIR__ . '/../utils/Response.php';
    Response::error('Internal server error.', 500);
}
