<?php
// php/config/response.php
declare(strict_types=1);

function is_fetch_request(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xrw    = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

    // Señales típicas de fetch/ajax:
    // - Accept: application/json
    // - X-Requested-With: fetch (lo seteamos desde JS)
    return (stripos($accept, 'application/json') !== false) || (strtolower($xrw) === 'fetch');
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function redirect_with_query(string $url, array $query = []): void
{
    $qs = http_build_query($query);
    $sep = (strpos($url, '?') === false) ? '?' : '&';
    header('Location: ' . $url . ($qs ? $sep . $qs : ''));
    exit;
}

function fail(string $message, int $status = 400): void
{
    if (is_fetch_request()) {
        json_response(['ok' => false, 'message' => $message], $status);
    }

    // fallback: vuelve al index con status por querystring
    redirect_with_query('/CreativeStudio/index.html', [
        'status' => 'error',
        'message' => $message
    ]);
}