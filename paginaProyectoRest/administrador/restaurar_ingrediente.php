<?php
session_start();

// Validar que el usuario esté logueado y sea un administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();

// Si la solicitud es GET, significa que el usuario presionó el botón en ingredientes_eliminados.php
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $usuario = $_SESSION['nombre']; // Nombre del usuario que restaura

    // Restaurar el ingrediente
    $sql = "UPDATE ingredientes SET activo = 1 WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        // Insertar en el historial
        $sql_historial = "INSERT INTO historial_cambios_ingredientes (ingrediente_id, accion, usuario, motivo) 
                          VALUES ($id, 'RESTAURAR', '$usuario', NULL)";
        if (!mysqli_query($conn, $sql_historial)) {
            echo "Error al registrar en el historial: " . mysqli_error($conn);
        }

        // Redirigir a la página con un mensaje de éxito
        header('Location: ingredientes_eliminados.php?restaurado=1');
    } else {
        echo "Error al restaurar el ingrediente: " . mysqli_error($conn);
    }
}

// Si la solicitud es POST, significa que se está usando el modal
if (isset($_POST['id']) && isset($_POST['motivo_restauracion'])) {
    $id = intval($_POST['id']);
    $motivo_restauracion = mysqli_real_escape_string($conn, $_POST['motivo_restauracion']);
    $usuario = $_SESSION['nombre']; // Nombre del usuario que restaura

    // Restaurar el ingrediente
    $sql = "UPDATE ingredientes SET activo = 1 WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        // Insertar el registro en historial_cambios_ingredientes con el motivo de restauración
        $sql_historial = "INSERT INTO historial_cambios_ingredientes (ingrediente_id, accion, usuario, motivo) 
                          VALUES ($id, 'RESTAURAR', '$usuario', '$motivo_restauracion')";
        if (mysqli_query($conn, $sql_historial)) {
            // Enviar respuesta de éxito si es AJAX
            echo 'success';
        } else {
            echo 'error_historial'; // Error al registrar el historial
        }
    } else {
        echo 'error_restaurar'; // Error al restaurar el ingrediente
    }
}
?>
