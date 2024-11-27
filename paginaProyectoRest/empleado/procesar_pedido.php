<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['nombre'])) {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();

// Verificar que se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $cliente_id = $_POST['cliente_id'];
    $mesa_id = $_POST['mesa_id'];
    $empleado_id = $_SESSION['id_empleado']; // Asumiendo que tienes el ID del empleado en la sesión
    $platillos = $_POST['platillos']; // Array de platillos y cantidades

    // Calcular el total del pedido y construir el detalle de los platillos
    $total = 0;
    $detalles = []; // Array para almacenar los nombres de los platillos
    foreach ($platillos as $platillo_id => $cantidad) {
        // Obtener el precio y nombre de cada platillo
        $query_platillo = "SELECT nombre, precio FROM platillos WHERE id = ?";
        $stmt_platillo = mysqli_prepare($conn, $query_platillo);
        mysqli_stmt_bind_param($stmt_platillo, "i", $platillo_id);
        mysqli_stmt_execute($stmt_platillo);
        $result_platillo = mysqli_stmt_get_result($stmt_platillo);
        $row_platillo = mysqli_fetch_assoc($result_platillo);

        $total += $row_platillo['precio'] * $cantidad;

        // Agregar el nombre del platillo a la lista de detalles
        $detalles[] = $row_platillo['nombre'] . ' x' . $cantidad; // Ejemplo: "Tacos x2"
    }

    // Unir todos los nombres de los platillos en una cadena separada por comas
    $detalles_str = implode(', ', $detalles);

    // Comenzar una transacción para asegurar la integridad de los datos
    mysqli_begin_transaction($conn);

    try {
        // Insertar en la tabla pedidos con los detalles como cadena
        $query_pedido = "INSERT INTO pedidos (cliente_id, mesa_id, empleado_id, estado, total, detalles) 
                         VALUES (?, ?, ?, 'pendiente', ?, ?)";
        $stmt_pedido = mysqli_prepare($conn, $query_pedido);
        mysqli_stmt_bind_param($stmt_pedido, "iiids", $cliente_id, $mesa_id, $empleado_id, $total, $detalles_str);
        mysqli_stmt_execute($stmt_pedido);
        
        // Obtener el ID del pedido recién insertado
        $pedido_id = mysqli_insert_id($conn);

        // Insertar los platillos en la tabla orden
        $query_orden = "INSERT INTO orden (pedido_id, platillo_id, cantidad) VALUES (?, ?, ?)";
        $stmt_orden = mysqli_prepare($conn, $query_orden);

        foreach ($platillos as $platillo_id => $cantidad) {
            mysqli_stmt_bind_param($stmt_orden, "iii", $pedido_id, $platillo_id, $cantidad);
            mysqli_stmt_execute($stmt_orden);
        }

        // Actualizar el estado de la mesa
        $query_mesa = "UPDATE mesas SET estado = 'ocupada' WHERE id = ?";
        $stmt_mesa = mysqli_prepare($conn, $query_mesa);
        mysqli_stmt_bind_param($stmt_mesa, "i", $mesa_id);
        mysqli_stmt_execute($stmt_mesa);

        // Confirmar la transacción
        mysqli_commit($conn);

        // Redirigir con mensaje de éxito
        $_SESSION['mensaje'] = "Pedido realizado con éxito";
        header('Location: verPedidos.php');
        exit();

    } catch (Exception $e) {
        // Si hay un error, revertir la transacción
        mysqli_rollback($conn);

        // Manejar el error
        $_SESSION['error'] = "Error al procesar el pedido: " . $e->getMessage();
        header('Location: indexE.php');
        exit();
    }
} else {
    // Si se intenta acceder directamente sin enviar un formulario
    header('Location: indexE.php');
    exit();
}
