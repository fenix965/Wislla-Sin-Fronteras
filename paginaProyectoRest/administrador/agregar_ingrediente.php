<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';

$conn = conexion(); // Conectar a la base de datos aquí

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Usar la conexión ya establecida para limpiar las entradas
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $cantidad_disponible = (int)$_POST['cantidad_disponible'];
    $unidad_medida = mysqli_real_escape_string($conn, $_POST['unidad_medida']);
    $precio_unitario = (float)$_POST['precio_unitario'];

    $sql_insert = "INSERT INTO ingredientes (nombre, cantidad_disponible, unidad_medida, precio_unitario) 
                   VALUES ('$nombre', $cantidad_disponible, '$unidad_medida', $precio_unitario)";

    if (mysqli_query($conn, $sql_insert)) {
        header('Location: inventario.php?mensaje=Ingrediente agregado exitosamente');
    } else {
        echo "Error al agregar el ingrediente: " . mysqli_error($conn);
    }

    mysqli_close($conn); // Cerrar la conexión al final
}
?>
