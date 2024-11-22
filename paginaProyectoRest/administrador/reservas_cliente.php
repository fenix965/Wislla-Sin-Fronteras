<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();

$cliente_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Obtener reservas del cliente
$sql = "SELECT r.*, c.nombre, c.apellidos 
        FROM reservas r 
        INNER JOIN clientes c ON r.cliente_id = c.id 
        WHERE c.id = ? AND r.eliminado = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

$cliente = null;
$reservas = [];
while ($row = $result->fetch_assoc()) {
    if (!$cliente) {
        $cliente = $row;
    }
    $reservas[] = $row;
}

// Guardar respuesta del administrador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_respuesta'])) {
    $reserva_id = $_POST['reserva_id'];
    $respuesta = $_POST['respuesta'];

    $sql = "UPDATE reservas SET respuesta_admin = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $respuesta, $reserva_id);
    $stmt->execute();

    header("Location: reservas_cliente.php?id=$cliente_id");
    exit();
}

// Eliminar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_reserva'])) {
    $reserva_id = $_POST['reserva_id'];
    $motivo = $_POST['motivo'];

    $sql = "UPDATE reservas 
            SET eliminado = 1, motivo_eliminacion = ?, fecha_eliminacion = NOW() 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $motivo, $reserva_id);
    $stmt->execute();

    header("Location: reservas_cliente.php?id=$cliente_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $cliente ? 'Reservas de ' . htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']) : 'Reservas'; ?></title>
    <link rel="stylesheet" href="../css/styleCliAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="--primary-brown">
            <h3><?= $cliente ? 'Reservas de ' . htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']) : 'Reservas'; ?></h3>
        </div>
        <div class="card-body">
            <?php if ($cliente && count($reservas) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Número de Personas</th>
                        <th>Total (Bs.)</th>
                        <th>Respuesta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= $reserva['id']; ?></td>
                        <td><?= date('d/m/Y', strtotime($reserva['fecha'])); ?></td>
                        <td><?= $reserva['numero_personas']; ?></td>
                        <td>Bs. <?= number_format($reserva['total'], 2); ?></td>
                        <td>
                            <form method="post" class="d-flex">
                                <input type="hidden" name="reserva_id" value="<?= $reserva['id']; ?>">
                                <select name="respuesta" class="form-select me-2">
                                    <option value="">Seleccionar respuesta</option>
                                    <option value="Confirmada">Confirmada</option>
                                    <option value="Requiere ajuste">Requiere ajuste</option>
                                    <option value="Cancelada">Cancelada</option>
                                </select>
                                <button type="submit" name="guardar_respuesta" class="btn btn-primary btn-sm">Guardar</button>
                            </form>
                        </td>
                        <td>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="reserva_id" value="<?= $reserva['id']; ?>">
                                <input type="text" name="motivo" class="form-control mb-2" placeholder="Motivo de eliminación" required>
                                <button type="submit" name="eliminar_reserva" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php elseif (!$cliente): ?>
            <p>No hay reservas registradas para este cliente.</p>
            <?php else: ?>
            <p>Cliente no encontrado.</p>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <a href="clientes.php" class="btn btn-secondary">Volver</a>
            <a href="reservas_eliminadas_cliente.php?id=<?= $cliente_id; ?>" class="btn btn-danger">Ver Reservas Eliminadas</a>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>