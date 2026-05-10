<?php
if (session_status() === PHP_SESSION_NONE) {
    $isProduction = strpos($_SERVER['HTTP_HOST'], 'railway.app') !== false;

    session_set_cookie_params([
        'lifetime' => 86400, // 24 hours
        'path'     => '/',
        'domain'   => $isProduction ? 'lost-and-found-production-7e24.up.railway.app' : '', 
        'secure'   => true,
        'httponly' => true,
        'samesite' => $isProduction ? 'None' : 'Lax', 
    ]);
    session_name('LOSTNFOUND_SESSID');
    session_start();
}