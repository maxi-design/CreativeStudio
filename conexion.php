<?php
    $conexion = mysqli_connect("localhost", "root", "", "suscripcion");

    if(!$conexion){
        echo 'Error al conectar la base de datos';
    } else {
        echo 'Conectado a la base de datos macho';
    }

?>