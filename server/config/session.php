<?php
/**
 * Session Configuration
 * 
 * Sets secure session flags before starting the session.
 * Must be included before any output is sent to the browser.
 */

if (session_status() === PHP_SESSION_NONE) {
    $isProduction = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $frontendDomain = 'lost-and-found-dusky.vercel.app';
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => $isProduction ? $frontendDomain : '', // Set domain for cross-site in prod
        'secure'   => $isProduction, // Secure only in production/HTTPS
        'httponly' => true,
        'samesite' => $isProduction ? 'None' : 'Lax', // None for cross-site, Lax for local
    ]);
    session_name('LOSTNFOUND_SESSID');
    session_start();
}
