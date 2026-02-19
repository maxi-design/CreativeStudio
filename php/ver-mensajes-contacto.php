<?php
header('Content-Type: application/json');

$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "chat_db";

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conn->connect_error) {
  die(json_encode(["error" => "Conexión fallida"]));
}

$sql = "SELECT nombre, apellidos, email, mensaje, fecha FROM formulario_contacto ORDER BY fecha DESC";
$resultado = $conn->query($sql);

$mensajes = [];
while ($fila = $resultado->fetch_assoc()) {
  $mensajes[] = $fila;
}

echo json_encode($mensajes);

$conn->close();
?>
