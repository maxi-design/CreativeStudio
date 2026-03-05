<?php
// php/admin/auth.php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        // Config simple para XAMPP/local
        session_start();
    }
}

function is_logged_in(): bool
{
    start_session();
    return !empty($_SESSION['admin_logged_in']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /CreativeStudio/admin/login.php');
        exit;
    }
}

function login(string $user, string $pass): bool
{
    start_session();

    if (!defined('ADMIN_USER') || !defined('ADMIN_PASS_HASH')) {
        return false;
    }

    if ($user !== ADMIN_USER) return false;
    if (!password_verify($pass, ADMIN_PASS_HASH)) return false;

    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user'] = $user;

    return true;
}

function logout(): void
{
    start_session();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            (bool)$params['secure'], (bool)$params['httponly']
        );
    }

    session_destroy();
}