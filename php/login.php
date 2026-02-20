<?php
session_start();

$usuario = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($usuario === 'admin' && $password === '1234') {
    $_SESSION['admin'] = true;
    header("Location: admin-chat.php");
} else {
    echo "<p style='color:red;'>Usuario o contraseña incorrectos</p>";
}
?>
