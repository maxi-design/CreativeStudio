<?php
    include "conexion.php";

    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $empresa = $_POST["empresa"];
    $servicio = $_POST["servicio"];
    $presupuesto = $_POST["presupuesto"];
    $proyecto = $_POST["proyecto"];

    $insertar = "INSERT INTO suscriptores(nombre, email, empresa, servicio, presupuesto, proyecto) VALUES('$nombre', '$email', '$empresa', '$servicio', '$presupuesto', '$proyecto')";

    $verificar_suscripcion = mysqli_query($conexion, "SELECT * FROM suscriptores WHERE email = '$email'");

    if(mysqli_num_rows($verificar_suscripcion) > 0){
        echo '<script>
                alert("El usuario ya esta registrado");
                window.history.go(-1);
            </script>';
        exit;
    }

    $resultado = mysqli_query($conexion, $insertar);

    if(!$resultado){
        echo 'Ha ocurrido un problema macho';
    } else {
        echo '<script>
                alert("Usuario registrado exitosamente");
                window.history.go(-1);
            </script>';
    }

    mysqli_close($conexion);
?>
