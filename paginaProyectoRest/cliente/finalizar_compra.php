<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Si no ha iniciado sesión, redirige al login
    header('Location: ../registro/login.php');
    exit();
}

// Incluye la conexión a la base de datos
include '../conexion.php';

$conn = conexion();

if (!$conn) {
    die("Error: no se pudo conectar a la base de datos.");
}

// Verifica si el carrito no está vacío
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    die("Error: el carrito está vacío.");
}

$cliente_id = $_SESSION['user_id'];
$mesa_id = $_POST['mesa_id'] ?? null; // Maneja el caso donde no se envíe la mesa_id
$fecha = date("Y-m-d H:i:s");
$estado = "pendiente";
$total = 0;
$detalles = [];

// Procesa el carrito
foreach ($_SESSION['carrito'] as $item) {
    // Asegúrate de que 'cantidad' esté definida y sea válida
    $cantidad = isset($item['cantidad']) ? $item['cantidad'] : 1; // Si no está definida, usa 1 por defecto
    $subtotal = $item['precio'] * $cantidad;
    $total += $subtotal;
    $detalles[] = $item['nombre'] . " x " . $cantidad;
}

// Convierte los detalles a texto
$detalles_texto = implode(", ", $detalles);

// Inserta el pedido en la base de datos
$query = "INSERT INTO pedidos (cliente_id, mesa_id, fecha, estado, total, detalles) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}

$stmt->bind_param("iissds", $cliente_id, $mesa_id, $fecha, $estado, $total, $detalles_texto);

if ($stmt->execute()) {
    // Limpia el carrito después de finalizar la compra
    unset($_SESSION['carrito']);
    echo json_encode(['success' => true, 'message' => 'Compra finalizada con éxito']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el pedido: ' . $stmt->error]);
}
?>
