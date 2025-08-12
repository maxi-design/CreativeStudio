<?php
// procesar_formulario.php

// Configuración de la base de datos para XAMPP (valores por defecto)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'suscripcion');

// Establecer conexión con la base de datos
try {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    
    // Configurar charset
    $conexion->set_charset("utf8");
    
    // Resto de tu código aquí...
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Inicializar variables para mensajes
$mensaje = '';
$error = false;

// Verificar si se envió el formulario
/*if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y sanitizar los datos del formulario*/
    $nombre = isset($_POST['nombre']) ? $conexion->real_escape_string(trim($_POST['nombre'])) : '';
    $email = isset($_POST['email']) ? $conexion->real_escape_string(trim($_POST['email'])) : '';
    $empresa = isset($_POST['empresa']) ? $conexion->real_escape_string(trim($_POST['empresa'])) : '';
    $servicio = isset($_POST['servicio']) ? $conexion->real_escape_string(trim($_POST['servicio'])) : '';
    $presupuesto = isset($_POST['presupuesto']) ? $conexion->real_escape_string(trim($_POST['presupuesto'])) : '';
    $proyecto = isset($_POST['proyecto']) ? $conexion->real_escape_string(trim($_POST['proyecto'])) : '';
    
    /*// Validar campos obligatorios
    if (empty($nombre) || empty($email) || empty($servicio) || empty($proyecto)) {
        $error = true;
        $mensaje = "Por favor complete todos los campos obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $mensaje = "Por favor ingrese un email válido.";
    } else {
        // Preparar la consulta SQL*/
        $sql = "INSERT INTO suscriptores (
            nombre, 
            email, 
            empresa, 
            servicio, 
            presupuesto, 
            proyecto, 
            fecha_registro
        ) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        // Preparar statement
        /*$stmt = $conexion->prepare($sql);
        
        if ($stmt) {
            // Vincular parámetros
            $stmt->bind_param(
                "ssssss", 
                $nombre, 
                $email, 
                $empresa, 
                $servicio, 
                $presupuesto, 
                $proyecto
            );
            
            // Ejecutar consulta
            if ($stmt->execute()) {
                $mensaje = "¡Gracias por tu interés! Hemos recibido tu solicitud y nos pondremos en contacto contigo pronto.";
                
                // Opcional: Enviar email de confirmación
                enviarEmailConfirmacion($nombre, $email);
            } else {
                $error = true;
                $mensaje = "Error al guardar los datos. Por favor intenta nuevamente más tarde.";
            }
            
            // Cerrar statement
            $stmt->close();
        } else {
            $error = true;
            $mensaje = "Error en la preparación de la consulta. Por favor intenta nuevamente más tarde.";
        }
    }
} else {
    $error = true;
    $mensaje = "Método de solicitud no válido.";
}

// Cerrar conexión
$conexion->close();

// Función para enviar email de confirmación
function enviarEmailConfirmacion($nombre, $email) {
    $asunto = "Confirmación de recepción de cotización";
    $mensaje = "Hola $nombre,\n\n";
    $mensaje .= "Hemos recibido tu solicitud de cotización correctamente.\n";
    $mensaje .= "Nuestro equipo revisará tu proyecto y se pondrá en contacto contigo en las próximas 24-48 horas.\n\n";
    $mensaje .= "Gracias por considerar nuestros servicios.\n\n";
    $mensaje .= "Atentamente,\nEl equipo de CreativeStudio";
    
    $cabeceras = "From: hola@creativestudio.com" . "\r\n" .
                 "Reply-To: hola@creativestudio.com" . "\r\n" .
                 "X-Mailer: PHP/" . phpversion();
    
    // Enviar email (comentado por defecto para evitar envíos accidentales)
    // mail($email, $asunto, $mensaje, $cabeceras);
}

// Devolver respuesta como JSON
header('Content-Type: application/json');
echo json_encode([
    'error' => $error,
    'mensaje' => $mensaje
]);*/

exit();
?>