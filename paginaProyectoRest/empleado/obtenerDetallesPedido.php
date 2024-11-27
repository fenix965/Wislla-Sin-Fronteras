<?php
include '../conexion.php';
$conn = conexion();

if (isset($_GET['orderId'])) {
    $orderId = $_GET['orderId'];

    // Obtener detalles del pedido
    function getOrderDetails($conn, $orderId) {
        $query = "SELECT 
                    pedidos.id, 
                    clientes.nombre AS cliente_nombre, 
                    clientes.apellidos AS cliente_apellido, 
                    mesas.numero AS mesa_numero, 
                    pedidos.total, 
                    pedidos.estado,
                    pedidos.fecha
                  FROM pedidos 
                  JOIN clientes ON pedidos.cliente_id = clientes.id
                  JOIN mesas ON pedidos.mesa_id = mesas.id
                  WHERE pedidos.id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $orderId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    $pedido = getOrderDetails($conn, $orderId);
    if ($pedido) {
        // Mostrar detalles del pedido
        echo "<h2>Detalles del Pedido #" . $pedido['id'] . "</h2>";
        echo "<p><strong>Cliente:</strong> " . $pedido['cliente_nombre'] . " " . $pedido['cliente_apellido'] . "</p>";
        echo "<p><strong>Mesa:</strong> " . $pedido['mesa_numero'] . "</p>";
        echo "<p><strong>Total:</strong> $" . number_format($pedido['total'], 2) . "</p>";
        echo "<p><strong>Fecha:</strong> " . $pedido['fecha'] . "</p>";
        echo "<p><strong>Estado:</strong> " . ucfirst($pedido['estado']) . "</p>";
    } else {
        echo "<p>No se encontraron detalles para este pedido.</p>";
    }
}
?>
