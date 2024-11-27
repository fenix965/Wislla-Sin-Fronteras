<?php
session_start();

// Validar que el usuario está logueado y tiene rol de 'administrador'
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

$nombre_admin = $_SESSION['nombre'];

include '../conexion.php';

// Conexión a la base de datos
$conn = conexion();

// Variable para el mensaje de éxito
$mensaje = '';
$mensaje_error = ''; // Mensaje de error
$mensaje_tipo = ''; // Tipo de mensaje (error, advertencia)

// Verificar si se ha enviado el formulario para agregar un ingrediente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'])) {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $cantidad_disponible = mysqli_real_escape_string($conn, $_POST['cantidad_disponible']);
    $unidad_medida = mysqli_real_escape_string($conn, $_POST['unidad_medida']);
    $precio_unitario = mysqli_real_escape_string($conn, $_POST['precio_unitario']);
    
    // Verificar si el ingrediente similar ya existe (ignorando mayúsculas y minúsculas)
    $sql_check = "SELECT * FROM ingredientes WHERE LOWER(nombre) LIKE LOWER('$nombre%')";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        // Si el ingrediente ya existe pero está eliminado
        $ingrediente_existente = mysqli_fetch_assoc($result_check);
        if ($ingrediente_existente['activo'] == 0) {
            // Ingrediente existe pero está eliminado
            $mensaje_error = 'El ingrediente "' . htmlspecialchars($nombre) . '" ya existe pero está eliminado. Por favor dirígete a la Página de Ingredientes eliminados para restaurarlo';
            $mensaje_tipo = 'warning'; // Advertencia
        } else {
            // Si el ingrediente existe y está activo
            $mensaje_error = 'El ingrediente con ese nombre ya existe.';
            $mensaje_tipo = 'danger'; // Error
        }
    } else {
        // Insertar el nuevo ingrediente en la base de datos
        $sql_insert = "INSERT INTO ingredientes (nombre, cantidad_disponible, unidad_medida, precio_unitario, activo) 
                       VALUES ('$nombre', '$cantidad_disponible', '$unidad_medida', '$precio_unitario', 1)";
        
        if (mysqli_query($conn, $sql_insert)) {
            // Establecer mensaje de éxito
            $mensaje = 'Ingrediente agregado exitosamente';
            $mensaje_tipo = 'success'; // Éxito
            // Redirigir para evitar el reenvío del formulario al actualizar la página
            header('Location: inventario.php');
            exit();
        } else {
            // Establecer mensaje de error
            $mensaje_error = 'Error al agregar ingrediente';
            $mensaje_tipo = 'danger'; // Error
        }
    }
}

// Consulta para obtener los ingredientes activos
$sql_ingredientes = "SELECT * FROM ingredientes WHERE activo = 1";  
$result_ingredientes = mysqli_query($conn, $sql_ingredientes);

if (!$result_ingredientes) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styleCliAdmin.css">
    <title>Ingredientes - Panel de Administración</title>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="logo">
                <a class="navbar-brand" href="#">
                    <img src="../imagenes/WisllaLogo.jpg" alt="Logo de Wislla">
                </a>
                <h1>Administración</h1>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="admin.php"><i class="fas fa-users"></i> Empleados</a></li>
                    <li><a href="clientes.php"><i class="fas fa-user-friends"></i> Clientes</a></li>
                    <li><a href="detalles.php"><i class="fas fa-receipt"></i> Detalles de Órdenes</a></li>
                    <li><a href="inventario.php" class="active"><i class="fas fa-boxes"></i> Inventario</a></li>
                    <li><a href="proveedores.php"><i class="fas fa-truck"></i> Proveedores</a></li>
                </ul>
            </nav>
            <div class="user-section">
                <span><i class="fas fa-user-shield"></i> <?= htmlspecialchars($nombre_admin); ?></span>
                <br><br>
                <a href="../registro/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="content-header">
                <h2><i class="fas fa-leaf"></i> Gestión de Ingredientes</h2>
            </header>

            <!-- Mensaje de éxito o error -->
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $mensaje_tipo; ?> alert-dismissible fade show" role="alert">
                    <strong><?= $mensaje; ?></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <!-- Modal de ingredientes duplicados -->
            <div class="modal fade" id="duplicateModal" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="duplicateModalLabel">Ingrediente Duplicado</h5>
                            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?= $mensaje_error ? htmlspecialchars($mensaje_error) : ''; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <section class="filter-section">
                <div class="row">
                    <div class="col">
                        <!-- Campo de búsqueda en tiempo real -->
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar ingrediente..." onkeyup="buscarIngredientes()">
                    </div>
                    <div class="col text-end">
                        <!-- Botón para agregar ingrediente -->
                        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addIngredientModal">
                            <i class="fas fa-plus"></i> Agregar Ingrediente
                        </button>
                        <!-- Botón para ver ingredientes eliminados -->
                        <a href="ingredientes_eliminados.php" class="btn btn-secondary mt-2" style="color: white;">
    <i class="fas fa-trash"></i> Ver Ingredientes Eliminados
</a>

                    </div>
                </div>
            </section>

            
            <section class="stats-card">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-utensils"></i> Nombre</th>
                                <th><i class="fas fa-cubes"></i> Cantidad Disponible</th>
                                <th><i class="fas fa-balance-scale"></i> Unidad de Medida</th>
                                <th><i class=""></i> Precio Unitario (Bs)</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result_ingredientes)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nombre']); ?></td>
                                    <td>
                                        <?php if($row['cantidad_disponible'] < 10): ?>
                                            <span class="badge bg-warning">
                                                <?= htmlspecialchars($row['cantidad_disponible']); ?>
                                            </span>
                                        <?php else: ?>
                                            <?= htmlspecialchars($row['cantidad_disponible']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['unidad_medida']); ?></td>
                                    <td class="amount-column">Bs <?= number_format($row['precio_unitario'], 2); ?></td>
                                    <td>
                                        <a href="editar_ingrediente.php?id=<?= $row['id']; ?>" style="color: white;" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="eliminar_ingrediente.php?id=<?= $row['id']; ?>" style="color: white;"  class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal para agregar ingrediente -->
    <div class="modal fade" id="addIngredientModal" tabindex="-1" aria-labelledby="addIngredientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addIngredientModalLabel">Agregar Ingrediente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="inventario.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Ingrediente</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="cantidad_disponible" class="form-label">Cantidad Disponible</label>
                            <input type="number" class="form-control" id="cantidad_disponible" name="cantidad_disponible" required>
                        </div>
                        <div class="mb-3">
                            <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                            <select class="form-select" id="unidad_medida" name="unidad_medida" required>
                                <option value="kilogramos">Kilogramos (kg)</option>
                                <option value="litros">Litros (l)</option>
                                <option value="unidades">Unidades</option>
                                <option value="mililitros">Mililitros (ml)</option>
                                <option value="gramos">Gramos (g)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="precio_unitario" class="form-label">Precio Unitario</label>
                            <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Agregar Ingrediente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este ingrediente?</p>
                    <form id="deleteForm">
                        <div class="mb-3">
                            <label for="motivo_eliminacion" class="form-label">Motivo de eliminación:</label>
                            <select class="form-select" id="motivo_eliminacion" name="motivo_eliminacion" required>
                                <option value="">Seleccione un motivo</option>
                                <option value="descontinuado">Producto descontinuado</option>
                                <option value="error_registro">Error en el registro</option>
                                <option value="sin_stock">Sin stock permanente</option>
                                <option value="cambio_proveedor">Cambio de proveedor</option>
                                <option value="otro">Otro motivo</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de error para eliminación sin motivo -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="errorModalLabel">Error en la Eliminación</h5>
                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Por favor seleccione un motivo para la eliminación del ingrediente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar el modal si existe un mensaje de error
        <?php if($mensaje_error): ?>
            var myModal = new bootstrap.Modal(document.getElementById('duplicateModal'), {});
            myModal.show();
        <?php endif; ?>

        // Script para el manejo de la eliminación
        document.addEventListener('DOMContentLoaded', function() {
            let deleteUrl = '';

            // Capturar el clic en los botones de eliminar
            document.querySelectorAll('a.btn-danger').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    deleteUrl = this.getAttribute('href');
                    let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    deleteModal.show();
                });
            });

            // Manejar la confirmación de eliminación
            document.getElementById('confirmDelete').addEventListener('click', function() {
                let motivo = document.getElementById('motivo_eliminacion').value;
                if (motivo) {
                    // Construir la URL correctamente
                    if (deleteUrl.includes('?')) {
                        deleteUrl += '&motivo=' + encodeURIComponent(motivo);
                    } else {
                        deleteUrl += '?motivo=' + encodeURIComponent(motivo);
                    }
                    window.location.href = deleteUrl;
                } else {
                    // Mostrar el modal de error si no se seleccionó un motivo
                    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {});
                    errorModal.show();
                }
            });
        });

        // Script para búsqueda en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('tbody tr');

            searchInput.addEventListener('input', function() {
                const filter = searchInput.value.toLowerCase();
                tableRows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = rowText.includes(filter) ? '' : 'none';
                });
            });
        });

    </script>
</body>
</html>
