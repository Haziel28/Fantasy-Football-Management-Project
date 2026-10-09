<?php
declare(strict_types=1);
// Connects PHP to PostgreSQL
function config(): array
{
    static $config = null;
    if ($config === null) {
        $path = __DIR__ . '/../config/config.php';
        if (!file_exists($path)) {
            die('Missing config/config.php. Copy config/config.example.php to config/config.php.');
        }
        $config = require $path;
    }
    return $config;
}

// Connect to the database and create a connection if one doesnt exist
function db(): PDO
{
    
    static $pdo = null;
    // Only create a new connection if one does not exist
    if ($pdo === null) {
        $c = config()['db'];

        // Create the connection string using the database settings
        $dsn = "pgsql:host={$c['host']};port={$c['port']};dbname={$c['name']}";

        // Connect to PostgreSQL using PDO
        $pdo = new PDO($dsn, $c['user'], $c['pass'], [

            // Throw an exception if a database error occurs
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    // Return the database connection
    return $pdo;
}
