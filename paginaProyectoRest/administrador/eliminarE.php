<?php
include '../conexion.php';
if (isset($_GET['id'])) {
    $empleado_id = $_GET['id']; // Obtener el ID del empleado a eliminar

    $conn = conexion();

    $sql_empleado = "DELETE FROM empleados WHERE id = '$empleado_id'";

    if (mysqli_query($conn, $sql_empleado)) {
        echo "Empleado eliminado correctamente.";
        header('Location: admin.php');
    } else {
        echo "Error al eliminar el empleado: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "ID de empleado no especificado.";
}
?>
