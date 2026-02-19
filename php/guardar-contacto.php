<?php
// Datos de conexión a la base de datos
$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "chat_db"; // Cambia esto si tu base se llama diferente

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Obtener y limpiar datos del formulario
$nombre = $conn->real_escape_string($_POST['nombre']);
$apellidos = $conn->real_escape_string($_POST['apellidos']);
$email = $conn->real_escape_string($_POST['email']);
$mensaje = $conn->real_escape_string($_POST['mensaje']);

// Insertar en la base de datos
$sql = "INSERT INTO formulario_contacto (nombre, apellidos, email, mensaje) VALUES ('$nombre', '$apellidos', '$email', '$mensaje')";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Mensaje enviado correctamente.'); window.location.href='contacto.html';</script>";
} else {
  echo "Error al guardar el mensaje: " . $conn->error;
}

$conn->close();
?>
