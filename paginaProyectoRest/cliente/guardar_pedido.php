<?php
session_start();
include '../conexion.php';

$conn = conexion();

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verifica que el carrito no esté vacío
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    die("El carrito está vacío.");
}

// Datos básicos del pedido
$cliente_id = $_SESSION['cliente_id']; // Asegúrate de que el cliente esté autenticado
$mesa_id = $_POST['mesa_id']; // O puedes usar un valor predeterminado si no se pasa
$fecha = date("Y-m-d");
$estado = "pendiente"; // Puedes ajustarlo según la lógica
$total = 0;

// Calcula el total del carrito
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// Inserta el pedido en la tabla "pedidos"
$query = "INSERT INTO pedidos (cliente_id, mesa_id, fecha, estado, total) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iissd", $cliente_id, $mesa_id, $fecha, $estado, $total);

if ($stmt->execute()) {
    $pedido_id = $stmt->insert_id; // Obtiene el ID del pedido recién creado

    // Inserta cada producto en una tabla de detalles (opcional)
    foreach ($_SESSION['carrito'] as $item) {
        $detalle_query = "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
        $detalle_stmt = $conn->prepare($detalle_query);
        $detalle_stmt->bind_param("iiid", $pedido_id, $item['id'], $item['cantidad'], $item['precio']);
        $detalle_stmt->execute();
    }

    // Limpia el carrito después de guardar
    unset($_SESSION['carrito']);

    // Redirige a la página de confirmación
    header("Location: compra_finalizada.php");
    exit;
} else {
    die("Error al guardar el pedido: " . $stmt->error);
}
?>