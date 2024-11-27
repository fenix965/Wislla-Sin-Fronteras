<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();

$cliente_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener reservas eliminadas del cliente
$sql = "SELECT r.*, c.nombre, c.apellidos 
        FROM reservas r 
        INNER JOIN clientes c ON r.cliente_id = c.id 
        WHERE c.id = ? AND r.eliminado = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

$cliente = null;
$reservas_eliminadas = [];
while ($row = $result->fetch_assoc()) {
    if (!$cliente) {
        $cliente = $row;
    }
    $reservas_eliminadas[] = $row;
}

// Reestablecer reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reestablecer_reserva'])) {
    $reserva_id = $_POST['reserva_id'];
    $motivo = $_POST['motivo'];

    $sql = "UPDATE reservas 
            SET eliminado = 0, motivo_reestablecimiento = ?, fecha_reestablecimiento = NOW() 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $motivo, $reserva_id);
    $stmt->execute();

    header("Location: reservas_eliminadas_cliente.php?id=$cliente_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $cliente ? 'Reservas Eliminadas de ' . htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']) : 'Reservas Eliminadas'; ?></title>
    <link rel="stylesheet" href="../css/styleCliAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h3><?= $cliente ? 'Reservas Eliminadas de ' . htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']) : 'Reservas Eliminadas'; ?></h3>
        </div>
        <div class="card-body">
            <?php if ($cliente && count($reservas_eliminadas) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Número de Personas</th>
                        <th>Total (Bs.)</th>
                        <th>Motivo de Eliminación</th>
                        <th>Fecha de Eliminación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas_eliminadas as $reserva): ?>
                    <tr>
                        <td><?= $reserva['id']; ?></td>
                        <td><?= date('d/m/Y', strtotime($reserva['fecha'])); ?></td>
                        <td><?= $reserva['numero_personas']; ?></td>
                        <td>Bs. <?= number_format($reserva['total'], 2); ?></td>
                        <td><?= htmlspecialchars($reserva['motivo_eliminacion']); ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($reserva['fecha_eliminacion'])); ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="reserva_id" value="<?= $reserva['id']; ?>">
                                <input type="text" name="motivo" class="form-control mb-2" placeholder="Motivo de reestablecimiento" required>
                                <button type="submit" name="reestablecer_reserva" class="btn btn-success btn-sm">Reestablecer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php elseif (!$cliente): ?>
            <p>No hay reservas eliminadas para este cliente.</p>
            <?php else: ?>
            <p>Cliente no encontrado.</p>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <a href="reservas_cliente.php?id=<?= $cliente_id; ?>" class="btn btn-primary">Volver</a>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>