<?php
/**
 * Standardized JSON Response Helper
 * 
 * Provides a consistent response format across all API endpoints:
 * { "success": boolean, "data": object/array|null, "message": string }
 */

class Response
{
    /**
     * Send a success response.
     *
     * @param mixed  $data    Response data (array, object, or null)
     * @param string $message Human-readable success message
     * @param int    $code    HTTP status code (default: 200)
     */
    public static function success(mixed $data = null, string $message = 'Success', int $code = 200): void
    {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send an error response.
     *
     * @param string $message Human-readable error message
     * @param int    $code    HTTP status code (default: 400)
     * @param mixed  $data    Optional error detail data
     */
    public static function error(string $message = 'An error occurred', int $code = 400, mixed $data = null): void
    {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'data'    => $data,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
