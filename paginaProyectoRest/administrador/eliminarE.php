<?php
session_start();

// Verificar que el usuario esté logueado y tenga permisos de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();

if (isset($_GET['id']) && isset($_GET['razon'])) {
    $empleado_id = $_GET['id'];
    $razon_despido = $_GET['razon'];

    // Verificar que los datos sean válidos
    if (!empty($empleado_id) && !empty($razon_despido)) {
        // Iniciar una transacción para asegurar que ambas acciones (insertar y eliminar) sean atómicas
        $conn->begin_transaction();

        try {
            // Insertar el motivo del despido en la tabla 'motivos_despido'
            $sql = "INSERT INTO motivos_despido (empleado_id, razon, fecha) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $empleado_id, $razon_despido);
            $stmt->execute();
            $stmt->close();

            // Eliminar al empleado de la tabla 'empleados'
            $sqlEliminar = "DELETE FROM empleados WHERE id = ?";
            $stmtEliminar = $conn->prepare($sqlEliminar);
            $stmtEliminar->bind_param("i", $empleado_id);
            $stmtEliminar->execute();
            $stmtEliminar->close();

            // Si todo fue correcto, hacer commit de la transacción
            $conn->commit();

            // Redirigir a la página de administración después de hacer las modificaciones
            header('Location: admin.php');
            exit();
        } catch (Exception $e) {
            // Si ocurre un error, hacer rollback de la transacción
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Faltan parámetros requeridos.";
    }
} else {
    echo "No se han proporcionado los datos necesarios.";
}

$conn->close();
?>
