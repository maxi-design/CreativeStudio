<?php
declare(strict_types=1);

require_once __DIR__ . '/../php/admin/auth.php';
logout();
header('Location: /CreativeStudio/admin/login.php');
exit;