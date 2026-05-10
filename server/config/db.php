<?php
/**
 * Database Configuration & PDO Connection
 * 
 * Returns a singleton PDO instance configured for MySQL
 * with secure defaults (exceptions, prepared statements).
 */



define('DB_URL', getenv('DATABASE_URL') ?: 'postgresql://neondb_owner:npg_F3rbmi4KznoM@ep-withered-firefly-aqrf3k94-pooler.c-8.us-east-1.aws.neon.tech/lost_and_found_db?sslmode=require&channel_binding=require');

/**
 * Get a PDO database connection instance.
 *
 * @return PDO
 * @throws PDOException
 */
function getDBConnection(): PDO
{
    static $pdo = null;


    if ($pdo === null) {
        // Use PostgreSQL DSN from DATABASE_URL
        $dsn = DB_URL;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, null, null, $options);
    }

    return $pdo;
}
