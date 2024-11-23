<?php
include '../conexion.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $motivo = isset($_GET['motivo']) ? $_GET['motivo'] : 'No especificado';
    $usuario = $_SESSION['nombre']; // Nombre del usuario que elimina

    $conn = conexion();

    // Actualizar el ingrediente como inactivo y guardar el motivo
    $sql = "UPDATE ingredientes SET activo = 0, motivo_eliminacion = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $motivo, $id);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            // Insertar el registro en historial_cambios_ingredientes
            $sql_historial = "INSERT INTO historial_cambios_ingredientes (ingrediente_id, accion, usuario, motivo) VALUES (?, 'ELIMINAR', ?, ?)";
            $stmt_historial = mysqli_prepare($conn, $sql_historial);
            
            if ($stmt_historial) {
                mysqli_stmt_bind_param($stmt_historial, "iss", $id, $usuario, $motivo);
                mysqli_stmt_execute($stmt_historial);
            }
            
            header('Location: inventario.php?mensaje=Ingrediente eliminad@ con éxito');
            exit();
        } else {
            header('Location: inventario.php?mensaje_error=Error al eliminar ingrediente');
            exit();
        }
    } else {
        die("Error en la consulta: " . mysqli_error($conn));
    }
} else {
    header('Location: inventario.php');
    exit();
}
?>
