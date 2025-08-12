<?php
    $enlace = mysqli_connect('localhost', 'root', '', 'suscripcion');

    if(!$enlace){
        die("No pudo conectarse a la base de datos" . mysqli_error());
    }
    echo "conexion exitosa";
    mysqli_close($enlace);
?>