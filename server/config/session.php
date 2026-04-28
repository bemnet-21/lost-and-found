<?php
/**
 * Session Configuration
 * 
 * Sets secure session flags before starting the session.
 * Must be included before any output is sent to the browser.
 */

if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters for security
    session_set_cookie_params([
        'lifetime' => 0,                // Session cookie (expires when browser closes)
        'path'     => '/',
        'domain'   => '',                // Current domain only
        'secure'   => false,             // Set to true in production with HTTPS
        'httponly'  => true,             // Prevent JavaScript access to session cookie
        'samesite' => 'Lax',            // CSRF protection
    ]);

    session_name('LOSTNFOUND_SESSID');
    session_start();
}
