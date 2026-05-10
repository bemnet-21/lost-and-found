<?php
/**
 * server/api/db.php
 */
function parsePgUrl($url) {
    $parts = parse_url($url);
    if (!$parts) throw new Exception('Invalid DATABASE_URL');
    
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s',
        $parts['host'],
        $parts['port'] ?? 5432,
        ltrim($parts['path'], '/'),
        $parts['user'],
        $parts['pass']
    );
    if (strpos($dsn, 'sslmode') === false) $dsn .= ';sslmode=require';
    return $dsn;
}

function getDBConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $url = getenv('DATABASE_URL');
        $dsn = parsePgUrl($url);
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}