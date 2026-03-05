<?php

// Database configuration example
// Copy this file to config.php and replace the values with your own credentials

define('DB_HOST', 'localhost');
define('DB_NAME', 'creativestudio');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

define('DB_CHARSET', 'utf8mb4');


// Admin credentials example
// Replace with your own username and hashed password

const ADMIN_USER = 'admin';
const ADMIN_PASS_HASH = 'your_password_hash_here';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    return $pdo;
}