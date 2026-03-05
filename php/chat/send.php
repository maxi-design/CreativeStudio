<?php
declare(strict_types=1);

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/response.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'message' => 'Método no permitido'], 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '', true);

$sender  = (string)($data['sender'] ?? 'visitor');
$content = trim((string)($data['content'] ?? ''));

if (!in_array($sender, ['visitor','bot'], true)) $sender = 'visitor';

if ($content === '' || mb_strlen($content) > 800) {
    json_response(['ok' => false, 'message' => 'Mensaje inválido (1 a 800 caracteres).'], 400);
}

try {
    $pdo = db();

    // ✅ Limpieza automática: mantener solo últimos 7 días
    $pdo->exec("DELETE FROM chat_messages WHERE created_at < (NOW() - INTERVAL 7 DAY)");

    $stmt = $pdo->prepare("INSERT INTO chat_messages (sender, content) VALUES (:sender, :content)");
    $stmt->execute([':sender' => $sender, ':content' => $content]);

    // Respuesta automática mínima para que se vea "vivo"
    if ($sender === 'visitor') {
        $auto = "✅ Recibí tu mensaje. Para poder responderte, por favor completá el formulario con tu nombre y tu email y nos pondremos en contacto cuanto antes. 👉 Ir al formulario: #contact";
        $stmt2 = $pdo->prepare("INSERT INTO chat_messages (sender, content) VALUES ('bot', :content)");
        $stmt2->execute([':content' => $auto]);
    }

    json_response(['ok' => true], 201);
} catch (Throwable $e) {
    json_response(['ok' => false, 'message' => 'No se pudo guardar el mensaje.'], 500);
}