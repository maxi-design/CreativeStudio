<?php
// Endpoint para obtener historial de chat.
// Actualmente el frontend no lo utiliza porque el chat inicia limpio
// en cada sesión del usuario.
declare(strict_types=1);

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/response.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    json_response(['ok' => false, 'message' => 'Método no permitido'], 405);
}

try {
    $pdo = db();

    $pdo->exec("DELETE FROM chat_messages WHERE created_at < (NOW() - INTERVAL 7 DAY)");

    // últimos 30 mensajes para mantenerlo rápido
    $stmt = $pdo->query("
        SELECT sender, content, created_at
        FROM chat_messages
        ORDER BY id DESC
        LIMIT 30
    ");
    $rows = $stmt->fetchAll();

    // devolver en orden cronológico
    $rows = array_reverse($rows);

    json_response($rows, 200);
} catch (Throwable $e) {
    json_response([], 200);
}