<?php
// php/contact/save.php
declare(strict_types=1);

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/response.php';

// Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    fail('Método no permitido.', 405);
}

// Soporta:
// - Formulario normal (application/x-www-form-urlencoded o multipart/form-data)
// - fetch() con FormData (también multipart/form-data)
$nombre  = trim((string)($_POST['nombre'] ?? ''));
$email   = trim((string)($_POST['email'] ?? ''));
$mensaje = trim((string)($_POST['mensaje'] ?? ''));

// Validación mínima y clara
if ($nombre === '' || mb_strlen($nombre) < 2 || mb_strlen($nombre) > 120) {
    fail('Nombre inválido. (2 a 120 caracteres)');
}

if ($email === '' || mb_strlen($email) > 180 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail('Email inválido.');
}

if ($mensaje === '' || mb_strlen($mensaje) < 10) {
    fail('Mensaje inválido. (mínimo 10 caracteres)');
}

// Guardar en DB con prepared statement
try {
    $pdo = db();
    $sql = "INSERT INTO contact_messages (nombre, email, mensaje) VALUES (:nombre, :email, :mensaje)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre'  => $nombre,
        ':email'   => $email,
        ':mensaje' => $mensaje,
    ]);

    if (is_fetch_request()) {
        json_response([
            'ok' => true,
            'message' => '¡Mensaje enviado! Te contactaremos pronto.'
        ], 201);
    }

    // Si fue envío tradicional:
    redirect_with_query('/CreativeStudio/index.html', [
        'status' => 'ok',
        'message' => 'Mensaje enviado correctamente.'
    ]);

} catch (Throwable $e) {
    // En producción podrías loguear $e->getMessage() a un archivo
    fail('No se pudo guardar el mensaje. Intenta nuevamente.', 500);
}