<?php
declare(strict_types=1);

require_once __DIR__ . '/../php/admin/auth.php';
require_login();

require_once __DIR__ . '/../php/config/config.php';

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$q = trim((string)($_GET['q'] ?? ''));
$hasQ = ($q !== '');

try {
    $pdo = db();

    if ($hasQ) {
        $like = '%' . $q . '%';

        $countStmt = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM contact_messages
            WHERE nombre LIKE :q OR email LIKE :q OR mensaje LIKE :q
        ");
        $countStmt->execute([':q' => $like]);
        $total = (int)($countStmt->fetch()['total'] ?? 0);

        $stmt = $pdo->prepare("
            SELECT id, nombre, email, mensaje, fecha
            FROM contact_messages
            WHERE nombre LIKE :q OR email LIKE :q OR mensaje LIKE :q
            ORDER BY fecha DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':q', $like, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
    } else {
        $total = (int)$pdo->query("SELECT COUNT(*) AS total FROM contact_messages")
            ->fetch()['total'];

        $stmt = $pdo->prepare("
            SELECT id, nombre, email, mensaje, fecha
            FROM contact_messages
            ORDER BY fecha DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
    }

    $totalPages = max(1, (int)ceil($total / $perPage));

} catch (Throwable $e) {
    $rows = [];
    $total = 0;
    $totalPages = 1;
    $error = $e->getMessage();
}

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Mensajes | Admin CreativeStudio</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; background:#0b1020; color:#eaeaf0; }
    .wrap { max-width:1100px; margin:32px auto; padding:0 16px; }
    .top { display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap; }
    .card { background:#111a33; border:1px solid rgba(255,255,255,.08); border-radius:14px; padding:16px; }
    .search { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    input { padding:10px 12px; border-radius:10px; border:1px solid rgba(255,255,255,.12); background:#0b1020; color:#fff; min-width:280px; }
    button, a.btn { padding:10px 12px; border-radius:10px; border:0; background:#4f46e5; color:#fff; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block; }
    a.ghost { background:transparent; border:1px solid rgba(255,255,255,.14); }
    table { width:100%; border-collapse:collapse; overflow:hidden; border-radius:12px; }
    th, td { text-align:left; padding:12px; border-bottom:1px solid rgba(255,255,255,.08); vertical-align:top; }
    th { font-size:12px; text-transform:uppercase; letter-spacing:.06em; opacity:.8; }
    .muted { opacity:.75; font-size:13px; }
    .msg { white-space:pre-wrap; max-width:520px; }
    .pager { display:flex; gap:8px; align-items:center; justify-content:flex-end; margin-top:14px; flex-wrap:wrap; }
    .badge { padding:6px 10px; border-radius:999px; background:rgba(255,255,255,.08); font-size:12px; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="top">
      <div>
        <h1 style="margin:0; font-size:22px;">Mensajes de Contacto</h1>
        <div class="muted">Total: <span class="badge"><?= (int)$total ?></span></div>
      </div>
      <div style="display:flex; gap:10px; align-items:center;">
        <a class="btn ghost" href="/CreativeStudio/index.html">Volver al sitio</a>
        <a class="btn" href="/CreativeStudio/admin/logout.php">Salir</a>
      </div>
    </div>

    <div class="card" style="margin-top:14px;">
      <form class="search" method="GET">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre, email o mensaje...">
        <button type="submit">Buscar</button>
        <?php if ($hasQ): ?>
          <a class="btn ghost" href="/CreativeStudio/admin/messages.php">Limpiar</a>
        <?php endif; ?>
        <span class="muted">Página <?= $page ?> / <?= $totalPages ?></span>
      </form>
    </div>

    <?php if (!empty($error ?? '')): ?>
      <div class="card" style="margin-top:14px; border-color: rgba(255,120,120,.3);">
        <strong>Error:</strong> <?= e((string)$error) ?>
      </div>
    <?php endif; ?>

    <div class="card" style="margin-top:14px; padding:0;">
      <table>
        <thead>
          <tr>
            <th style="width:70px;">ID</th>
            <th style="width:160px;">Nombre</th>
            <th style="width:220px;">Email</th>
            <th>Mensaje</th>
            <th style="width:170px;">Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="5" class="muted" style="padding:18px;">No hay mensajes para mostrar.</td></tr>
          <?php else: ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td><?= e((string)$r['nombre']) ?></td>
                <td><a href="mailto:<?= e((string)$r['email']) ?>" style="color:#b9b4ff;"><?= e((string)$r['email']) ?></a></td>
                <td class="msg"><?= e((string)$r['mensaje']) ?></td>
                <td class="muted"><?= e((string)$r['fecha']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="pager" style="padding:14px;">
        <?php
          $base = '/CreativeStudio/admin/messages.php';
          $qs = [];
          if ($hasQ) $qs['q'] = $q;

          $prev = max(1, $page - 1);
          $next = min($totalPages, $page + 1);

          $qsPrev = http_build_query(array_merge($qs, ['page' => $prev]));
          $qsNext = http_build_query(array_merge($qs, ['page' => $next]));
        ?>
        <a class="btn ghost" href="<?= $base . '?' . $qsPrev ?>">Anterior</a>
        <span class="badge">Página <?= $page ?></span>
        <a class="btn ghost" href="<?= $base . '?' . $qsNext ?>">Siguiente</a>
      </div>
    </div>
  </div>
</body>
</html>