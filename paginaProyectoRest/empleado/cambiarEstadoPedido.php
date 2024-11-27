<?php
session_start();

if (!isset($_SESSION['nombre'])) {
    // Si no está logueado, redirige al login
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();

// Verificar si el formulario ha sido enviado
if (isset($_POST['pedido_id']) && isset($_POST['nuevo_estado'])) {
    $pedido_id = $_POST['pedido_id'];
    $nuevo_estado = $_POST['nuevo_estado'];
    
    // Validar los estados posibles
    $estados_validos = ['pendiente', 'completado', 'cancelado'];
    if (in_array($nuevo_estado, $estados_validos)) {
        // Consulta para actualizar el estado del pedido
        $consulta_actualizacion = "UPDATE pedidos SET estado = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $consulta_actualizacion);
        mysqli_stmt_bind_param($stmt, "si", $nuevo_estado, $pedido_id);
        if (mysqli_stmt_execute($stmt)) {
            // Si la actualización es exitosa, redirigir a la página de pedidos
            header("Location: verPedidos.php");
            exit();
        } else {
            // Si hay algún error en la ejecución, mostrar mensaje
            echo "Error al actualizar el estado del pedido.";
        }
    } else {
        // Si el estado no es válido, mostrar mensaje de error
        echo "Estado inválido.";
    }
} else {
    // Si no se reciben los parámetros esperados, redirigir de vuelta
    header("Location: verPedidos.php");
    exit();
}

?>
