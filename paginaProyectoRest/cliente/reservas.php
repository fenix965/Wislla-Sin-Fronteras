<?php
session_start();
require_once '../conexion.php';

$db = conexion();
if (!$db) {
    die("Error de conexión: " . mysqli_connect_error());
}

if (!isset($_SESSION['nombre'])) {
    header("Location: ../registro/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

function calculateTotal($mesas, $db) {
    $precios = [
        'Planta Baja' => 50,
        'Planta Alta' => 70,
        'Terraza' => 60,
        'Salón Privado' => 100,
        'default' => 40
    ];

    $total = 0;
    foreach ($mesas as $mesa_id) {
        $result = mysqli_query($db, "SELECT ubicacion FROM mesas WHERE id = $mesa_id");
        $zona = mysqli_fetch_assoc($result)['ubicacion'];
        $total += $precios[$zona] ?? $precios['default'];
    }
    return $total;
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $mesas = $_POST['mesas'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $num_personas = $_POST['numero_personas'];
        $observaciones = $_POST['observaciones'] ?? '';

        // Validaciones (mismo código anterior)
        // ... [previous validation logic remains the same]

        $total = calculateTotal($mesas, $db);

        mysqli_begin_transaction($db);

        $query = "INSERT INTO reservas (user_id, fecha, hora, numero_personas, total, observaciones)
                  VALUES ('$user_id', '$fecha', '$hora', $num_personas, $total, '$observaciones')";
        
        if (!mysqli_query($db, $query)) {
            throw new Exception("Error al crear la reserva: " . mysqli_error($db));
        }
        
        $reserva_id = mysqli_insert_id($db);

        foreach ($mesas as $mesa_id) {
            $query = "INSERT INTO reservas_mesas (reserva_id, mesa_id) VALUES ($reserva_id, $mesa_id)";
            if (!mysqli_query($db, $query)) {
                throw new Exception("Error al asociar mesa: " . mysqli_error($db));
            }
        }

        mysqli_commit($db);
        $mensaje = "¡Reserva realizada con éxito! Total a pagar: Bs" . number_format($total, 2);
        $tipo_mensaje = "success";
    } catch (Exception $e) {
        mysqli_rollback($db);
        $mensaje = $e->getMessage();
        $tipo_mensaje = "error";
    }
}

$mesas_por_ubicacion = [];
$query = "SELECT id, numero, capacidad, ubicacion, imagen FROM mesas ORDER BY ubicacion, numero";
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $mesas_por_ubicacion[$row['ubicacion']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Mesas - Restaurante</title>
    <link rel="stylesheet" href="../css/styleReservas.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container reservation-container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Reserva de Mesas</h1>

                <?php if ($mensaje): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje === 'success' ? 'success' : 'danger'; ?>">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="reservation-form" onsubmit="return validarFormulario();">
                <div class="row reservation-details">
                    <div class="col-md-4 reservation-input">
                        <div class="input-wrapper">
                            <i class="icon-calendar"></i>
                            <label for="fecha" class="floating-label">Fecha de Reserva</label>
                            <input type="date" class="custom-input" id="fecha" name="fecha" required>
                        </div>
                        <br><br>
                    </div>
                    <div class="col-md-4 reservation-input">
                        <div class="input-wrapper">
                            <i class="icon-clock"></i>
                            <label for="hora" class="floating-label">Hora de Reserva</label>
                            <input type="time" class="custom-input" id="hora" name="hora" required>
                        </div>
                    </div>
                    <br><br>
                    <div class="col-md-4 reservation-input">
                        <div class="input-wrapper">
                            <i class="icon-users"></i>
                            <label for="numero_personas" class="floating-label">Número de Personas</label>
                            <input type="number" class="custom-input" id="numero_personas" name="numero_personas" min="1" max="50" required>
                        </div>
                    </div>
                </div>
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones Adicionales</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Comentarios especiales, alergias, etc."></textarea>
                        </div>
                    </div>

                    <h2 class="mb-4">Seleccione sus Mesas</h2>

                    <?php foreach ($mesas_por_ubicacion as $ubicacion => $mesas): ?>
                <div class="location-section">
                    <h2 class="location-title"><?php echo $ubicacion; ?></h2>
                    <div class="row">
                        <?php foreach ($mesas as $mesa): ?>
                            <div class="col-md-4 mb-4">
                                <div class="table-option position-relative">
                                    <span class="table-number">#<?php echo $mesa['numero']; ?></span>
                                    
                                    <?php
                                    $imagen_binaria = $mesa['imagen'];
                                    $imagen_base64 = base64_encode($imagen_binaria);
                                    $imagen_data_url = 'data:image/jpeg;base64,' . $imagen_base64;
                                    ?>
                                    <img src="<?php echo $imagen_data_url; ?>" 
                                         class="table-image" 
                                         alt="Mesa <?php echo $mesa['numero']; ?>">
                                    
                                    <div class="table-details">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   id="mesa-<?php echo $mesa['id']; ?>" 
                                                   name="mesas[]" 
                                                   value="<?php echo $mesa['id']; ?>"
                                                   data-capacidad="<?php echo $mesa['capacidad']; ?>">
                                            <label class="form-check-label" for="mesa-<?php echo $mesa['id']; ?>">
                                                <?php echo $mesa['capacidad']; ?> Personas
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary me-2">Confirmar Reserva</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='../cliente/paginaInfo.php'">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function validarFormulario() {
        const fecha = document.getElementById('fecha').value;
        const hora = document.getElementById('hora').value;
        const numeroPersonas = document.getElementById('numero_personas').value;
        const mesasSeleccionadas = document.querySelectorAll('input[name="mesas[]"]:checked');

        if (!fecha || !hora || !numeroPersonas || mesasSeleccionadas.length === 0) {
            alert('Por favor, complete todos los campos y seleccione al menos una mesa.');
            return false;
        }

        let capacidadSeleccionada = 0;
        mesasSeleccionadas.forEach(mesa => {
            capacidadSeleccionada += parseInt(mesa.getAttribute('data-capacidad'));
        });

        if (capacidadSeleccionada < numeroPersonas) {
            alert('El número de personas excede la capacidad total de las mesas seleccionadas.');
            return false;
        }

        return true;
    }
    </script>
</body>
</html>