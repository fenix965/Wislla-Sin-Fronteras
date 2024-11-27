<?php
include '../conexion.php';

$conn = conexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empleado_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $razon = trim($_POST['razon']);
    $fecha = date('Y-m-d');

    if (!$empleado_id || empty($razon)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Datos inválidos o incompletos.'
        ]);
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
        // Obtener el usuario_id del empleado
        $stmt_usuario_id = $conn->prepare("SELECT usuario_id FROM empleados WHERE id = ?");
        $stmt_usuario_id->bind_param("i", $empleado_id);
        $stmt_usuario_id->execute();
        $stmt_usuario_id->bind_result($usuario_id);
        $stmt_usuario_id->fetch();
        $stmt_usuario_id->close();

        if (!$usuario_id) {
            throw new Exception("No se encontró el empleado con el ID especificado.");
        }

        // Insertar motivo de despido
        $stmt_motivos = $conn->prepare("INSERT INTO motivos_despido (empleado_id, razon, fecha) VALUES (?, ?, ?)");
        $stmt_motivos->bind_param("iss", $empleado_id, $razon, $fecha);
        if (!$stmt_motivos->execute()) {
            throw new Exception("Error al insertar motivo de despido: " . $stmt_motivos->error);
        }
        $stmt_motivos->close();

        // Eliminar el empleado
        $stmt_borrar_empleado = $conn->prepare("DELETE FROM empleados WHERE id = ?");
        $stmt_borrar_empleado->bind_param("i", $empleado_id);
        if (!$stmt_borrar_empleado->execute()) {
            throw new Exception("Error al borrar empleado: " . $stmt_borrar_empleado->error);
        }
        $stmt_borrar_empleado->close();

        // Eliminar el usuario relacionado
        $stmt_borrar_usuario = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt_borrar_usuario->bind_param("i", $usuario_id);
        if (!$stmt_borrar_usuario->execute()) {
            throw new Exception("Error al borrar usuario: " . $stmt_borrar_usuario->error);
        }
        $stmt_borrar_usuario->close();

        // Confirmar transacción
        mysqli_commit($conn);

        echo json_encode([
            'status' => 'success',
            'message' => 'Empleado despedido correctamente.'
        ]);
    } catch (Exception $e) {
        mysqli_rollback($conn);

        echo json_encode([
            'status' => 'error',
            'message' => 'Error al procesar el despido: ' . $e->getMessage()
        ]);
    }

    $conn->close();
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido.'
    ]);
}
?>
