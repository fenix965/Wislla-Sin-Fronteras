<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $usuario = $_SESSION['nombre'];

    $sql = "UPDATE ingredientes SET activo = 1 WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $sql_historial = "INSERT INTO historial_cambios_ingredientes (ingrediente_id, accion, usuario, motivo) 
                          VALUES ($id, 'RESTAURAR', '$usuario', NULL)";
        if (!mysqli_query($conn, $sql_historial)) {
            echo "Error al registrar en el historial: " . mysqli_error($conn);
        }

        header('Location: ingredientes_eliminados.php?restaurado=1');
    } else {
        echo "Error al restaurar el ingrediente: " . mysqli_error($conn);
    }
}

if (isset($_POST['id']) && isset($_POST['motivo_restauracion'])) {
    $id = intval($_POST['id']);
    $motivo_restauracion = mysqli_real_escape_string($conn, $_POST['motivo_restauracion']);
    $usuario = $_SESSION['nombre'];

    $sql = "UPDATE ingredientes SET activo = 1 WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $sql_historial = "INSERT INTO historial_cambios_ingredientes (ingrediente_id, accion, usuario, motivo) 
                          VALUES ($id, 'RESTAURAR', '$usuario', '$motivo_restauracion')";
        if (mysqli_query($conn, $sql_historial)) {
            echo 'success';
        } else {
            echo 'error_historial';
        }
    } else {
        echo 'error_restaurar';
    }
}
?>
