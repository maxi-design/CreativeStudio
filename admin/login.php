<?php
declare(strict_types=1);

require_once __DIR__ . '/../php/admin/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim((string)($_POST['user'] ?? ''));
    $pass = (string)($_POST['pass'] ?? '');

    if (login($user, $pass)) {
        header('Location: /CreativeStudio/admin/messages.php');
        exit;
    }

    $error = 'Usuario o contraseña inválidos.';
}

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin Login | CreativeStudio</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; background:#0b1020; color:#eaeaf0; }
    .card { max-width:420px; margin:64px auto; background:#111a33; border:1px solid rgba(255,255,255,.08); border-radius:14px; padding:24px; }
    label { display:block; margin:12px 0 6px; font-size:14px; opacity:.9; }
    input { width:100%; padding:12px 14px; border-radius:10px; border:1px solid rgba(255,255,255,.12); background:#0b1020; color:#fff; }
    button { width:100%; margin-top:16px; padding:12px 14px; border-radius:10px; border:0; background:#4f46e5; color:#fff; font-weight:700; cursor:pointer; }
    .error { margin-top:12px; color:#ffb4b4; font-size:14px; }
    .hint { margin-top:10px; font-size:12px; opacity:.7; }
  </style>
</head>
<body>
  <div class="card">
    <h1 style="margin:0 0 6px; font-size:20px;">Panel Admin</h1>
    <p style="margin:0 0 16px; opacity:.8;">Ingresá para ver los mensajes del formulario.</p>

    <form method="POST" autocomplete="off">
      <label>Usuario</label>
      <input type="text" name="user" required>

      <label>Contraseña</label>
      <input type="password" name="pass" required>

      <button type="submit">Ingresar</button>

      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <div class="hint">Tip: cambia las credenciales en <code>php/config/config.php</code></div>
    </form>
  </div>
</body>
</html>