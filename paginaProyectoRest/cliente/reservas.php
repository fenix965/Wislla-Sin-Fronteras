<?php
session_start();

// Incluir el archivo de conexión
require_once '../conexion.php';

// Establecer la conexión usando tu función
$db = conexion();

// Verificar si la conexión fue exitosa
if (!$db) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Validar si el usuario está loggeado
if (!isset($_SESSION['nombre'])) {
    header("Location: ../registro/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Función para calcular el total de la reserva
function calculateTotal($mesas, $db) {
    $total = 0;
    foreach ($mesas as $mesa_id) {
        $result = mysqli_query($db, "SELECT ubicacion FROM mesas WHERE id = $mesa_id");
        $zona = mysqli_fetch_assoc($result)['ubicacion'];

        switch ($zona) {
            case 'Planta Baja':
                $precio = 50;
                break;
            case 'Planta Alta':
                $precio = 70;
                break;
            case 'Terraza':
                $precio = 60;
                break;
            case 'Salón Privado':
                $precio = 100;
                break;
            default:
                $precio = 40;
                break;
        }
        $total += $precio;
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

        // Validar fecha
        if (strtotime($fecha) < strtotime(date('Y-m-d'))) {
            throw new Exception("La fecha debe ser igual o posterior al día actual.");
        }

        // Validar capacidad total
        $capacidad_total = 0;
        foreach ($mesas as $mesa_id) {
            $result = mysqli_query($db, "SELECT capacidad FROM mesas WHERE id = $mesa_id");
            $capacidad_total += mysqli_fetch_assoc($result)['capacidad'];
        }

        if ($num_personas > $capacidad_total) {
            throw new Exception("El número de personas excede la capacidad de las mesas seleccionadas.");
        }

        // Validar disponibilidad de las mesas
        $hora_fin = date('H:i:s', strtotime($hora) + 2 * 60 * 60);
        foreach ($mesas as $mesa_id) {
            $query = "SELECT * FROM reservas r
                      JOIN reservas_mesas rm ON r.id = rm.reserva_id
                      WHERE rm.mesa_id = $mesa_id
                      AND r.fecha = '$fecha'
                      AND ('$hora' BETWEEN r.hora AND ADDTIME(r.hora, '02:00:00')
                           OR '$hora_fin' BETWEEN r.hora AND ADDTIME(r.hora, '02:00:00'))";
            $result = mysqli_query($db, $query);

            if (mysqli_num_rows($result) > 0) {
                throw new Exception("La mesa $mesa_id no está disponible en el horario seleccionado.");
            }
        }

        // Calcular total
        $total = calculateTotal($mesas, $db);

        // Iniciar transacción
        mysqli_begin_transaction($db);

        // Insertar reserva
        $query = "INSERT INTO reservas (cliente_id, mesa_id, fecha, hora, numero_personas, total)
                  VALUES ('$user_id','$mesa_id', '$fecha', '$hora', $num_personas, $total)";
        if (!mysqli_query($db, $query)) {
            throw new Exception("Error al crear la reserva: " . mysqli_error($db));
        }
        $reserva_id = mysqli_insert_id($db);

        // Insertar mesas asociadas
        foreach ($mesas as $mesa_id) {
            $query = "INSERT INTO reservas_mesas (reserva_id, mesa_id) VALUES ($reserva_id, $mesa_id)";
            if (!mysqli_query($db, $query)) {
                throw new Exception("Error al asociar mesa: " . mysqli_error($db));
            }
        }

        // Actualizar estado de las mesas
        foreach ($mesas as $mesa_id) {
            $query = "UPDATE mesas SET estado = 'reservada', estado_reservado_hasta = '$hora_fin' WHERE id = $mesa_id";
            if (!mysqli_query($db, $query)) {
                throw new Exception("Error al actualizar estado de mesa: " . mysqli_error($db));
            }
        }

        mysqli_commit($db);
        $mensaje = "¡Reserva realizada con éxito! Total a pagar: $" . number_format($total, 2);
        $tipo_mensaje = "success";
    } catch (Exception $e) {
        mysqli_rollback($db);
        $mensaje = $e->getMessage();
        $tipo_mensaje = "error";
    }
}

$mesas_por_ubicacion = [];
$query = "SELECT id, numero, capacidad, ubicacion, imagen FROM mesas WHERE estado = 'disponible' ORDER BY ubicacion, numero";
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
    <title>Reservar Mesa</title>
    <link rel="stylesheet" href="../css/styleReservas.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Reservar Mesa</h1>
        
        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validarFormulario();">
            <div class="form-group">
                <label for="fecha">Fecha de reserva:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>

            <div class="form-group">
                <label for="hora">Hora de reserva:</label>
                <input type="time" id="hora" name="hora" required>
            </div>

            <div class="form-group">
                <label for="numero_personas">Número de personas:</label>
                <input type="number" id="numero_personas" name="numero_personas" min="1" required>
            </div>

            <div class="mesas-section">
                <label>Seleccionar mesas:</label>
                <?php foreach ($mesas_por_ubicacion as $ubicacion => $mesas): ?>
                    <div class="ubicacion-group">
                        <h3 class="ubicacion-title"><?php echo $ubicacion; ?></h3>
                        <div class="mesas-grid">
                        <?php foreach ($mesas as $mesa): ?>
                            <div class="table-option">
                                <span class="table-number">Mesa #<?php echo $mesa['numero']; ?></span>

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
                                               class="form-check-input mesa-checkbox" 
                                               id="mesa-<?php echo $mesa['id']; ?>" 
                                               name="mesas[]" 
                                               value="<?php echo $mesa['id']; ?>"
                                               data-capacidad="<?php echo $mesa['capacidad']; ?>">
                                        <label class="form-check-label" for="mesa-<?php echo $mesa['id']; ?>">
                                            Seleccionar
                                        </label>
                                    </div>
                                    <div class="precio-info">
                                        Capacidad: <?php echo $mesa['capacidad']; ?> personas
                                    </div>
                                    <div class="precio-info">
                                        Precio: $<?php
                                        switch ($ubicacion) {
                                            case 'Planta Baja':
                                                echo "50";
                                                break;
                                            case 'Planta Alta':
                                                echo "70";
                                                break;
                                            case 'Terraza':
                                                echo "60";
                                                break;
                                            case 'Salón Privado':
                                                echo "100";
                                                break;
                                            default:
                                                echo "40";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit">Confirmar Reserva</button>
            <button onclick="window.location.href='../cliente/paginaInfo.php'" class="back-button">Volver al Inicio</button>
        </form>
    </div>

    <script>
        function mostrarNotificacion(mensaje, tipo = 'error') {
    // Crear contenedor de notificación si no existe
    let notificacionContainer = document.getElementById('notificacion-container');
    if (!notificacionContainer) {
        notificacionContainer = document.createElement('div');
        notificacionContainer.id = 'notificacion-container';
        notificacionContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 300px;
        `;
        document.body.appendChild(notificacionContainer);
    }

    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.style.cssText = `
        background-color: ${tipo === 'error' ? '#f8d7da' : '#d4edda'};
        color: ${tipo === 'error' ? '#721c24' : '#155724'};
        border: 1px solid ${tipo === 'error' ? '#f5c6cb' : '#c3e6cb'};
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    `;
    notificacion.textContent = mensaje;

    // Añadir y mostrar la notificación
    notificacionContainer.appendChild(notificacion);
    setTimeout(() => {
        notificacion.style.opacity = '1';
    }, 10);

    // Ocultar y eliminar después de 5 segundos
    setTimeout(() => {
        notificacion.style.opacity = '0';
        setTimeout(() => {
            notificacionContainer.removeChild(notificacion);
        }, 300);
    }, 5000);
}

function validarFormulario() {
    const fecha = document.getElementById('fecha').value;
    const hora = document.getElementById('hora').value;
    const numPersonas = parseInt(document.getElementById('numero_personas').value);
    const mesasSeleccionadas = document.querySelectorAll('input[name="mesas[]"]:checked');
    
    // Validar fecha
    const hoy = new Date().toISOString().split('T')[0];
    if (fecha < hoy) {
        mostrarNotificacion('La fecha no puede ser anterior al día de hoy.');
        return false;
    }

    // Validar que se haya seleccionado al menos una mesa
    if (mesasSeleccionadas.length === 0) {
        mostrarNotificacion('Por favor, seleccione al menos una mesa.');
        return false;
    }

    // Validar capacidad total
    let capacidadTotal = 0;
    mesasSeleccionadas.forEach(mesa => {
        capacidadTotal += parseInt(mesa.dataset.capacidad);
    });

    if (numPersonas > capacidadTotal) {
        mostrarNotificacion('El número de personas excede la capacidad de las mesas seleccionadas.');
        return false;
    }

    // Validar horario de reserva
    const horaNum = parseInt(hora.split(':')[0]);
    if (horaNum < 12 || horaNum >= 23) {
        mostrarNotificacion('Las reservas solo están disponibles entre las 12:00 y las 23:00.');
        return false;
    }

    return true;
}
    </script>
</body>
</html>