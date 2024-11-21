<?php
include '../conexion.php';

// Verificar que el usuario esté autenticado y tenga el rol adecuado
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

// Obtener el ID del ingrediente y el motivo de eliminación
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $motivo = isset($_GET['motivo']) ? $_GET['motivo'] : 'No especificado';
    
    // Conexión a la base de datos
    $conn = conexion();

    // Actualizar el ingrediente para marcarlo como inactivo y guardar el motivo
    $sql = "UPDATE ingredientes SET activo = 0, motivo_eliminacion = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $motivo, $id);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            // Redirigir al inventario con un mensaje de éxito
            header('Location: inventario.php?mensaje=Ingrediente eliminad@ con éxito');
            exit();
        } else {
            // Redirigir con un mensaje de error
            header('Location: inventario.php?mensaje_error=Error al eliminar ingrediente');
            exit();
        }
    } else {
        // En caso de fallo en la consulta SQL
        die("Error en la consulta: " . mysqli_error($conn));
    }
} else {
    // Redirigir si no se pasa el ID
    header('Location: inventario.php');
    exit();
}
?>