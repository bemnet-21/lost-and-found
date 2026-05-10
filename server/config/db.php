<?php
/**
 * Database Configuration & PDO Connection for PostgreSQL (Neon)
 *
 * Parses DATABASE_URL and builds a valid PDO DSN.
 */

function parsePgUrl($url) {
    $parts = parse_url($url);
    if (!$parts || !isset($parts['host'], $parts['user'], $parts['pass'], $parts['path'])) {
        throw new Exception('Invalid DATABASE_URL');
    }
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s',
        $parts['host'],
        $parts['port'] ?? 5432,
        ltrim($parts['path'], '/'),
        $parts['user'],
        $parts['pass']
    );
    // Add sslmode=require if present in query
    if (isset($parts['query']) && strpos($parts['query'], 'sslmode=require') !== false) {
        $dsn .= ';sslmode=require';
    }
    return $dsn;
}

function getDBConnection(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $url = getenv('DATABASE_URL');
        if (!$url) {
            throw new Exception('DATABASE_URL environment variable not set');
        }
        $dsn = parsePgUrl($url);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, null, null, $options);
    }
    return $pdo;
}
