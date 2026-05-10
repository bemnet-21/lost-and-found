<?php
/**
 * CORS Headers Configuration
 * 
 * Sets the required Cross-Origin Resource Sharing headers
 * for allowing the React frontend at localhost:3000 to
 * communicate with this API.
 *
 * Must be included at the top of every API entry point.
 */

$allowedOrigins = [
    'https://lost-and-found-dusky.vercel.app',
    'http://localhost:3000',
];
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: https://lost-and-found-dusky.vercel.app');
}
header('Access-Control-Allow-Credentials: true');

// Allowed HTTP methods
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Allowed request headers
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With');

// Cache preflight response for 0 seconds (disable cache while debugging)
header('Access-Control-Max-Age: 0');

// Set default content type to JSON
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
