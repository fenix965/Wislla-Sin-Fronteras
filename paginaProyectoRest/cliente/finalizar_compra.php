<?php
session_start();

// Si ya existe un carrito de compras, lo vaciamos
if (isset($_SESSION['carrito'])) {
    unset($_SESSION['carrito']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Finalizada - Wislla Sin Fronteras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilo para la notificación personalizada */
        .custom-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50; /* Verde */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: white;
            font-family: 'Poppins', sans-serif;
            z-index: 1000;
            max-width: 350px;
        }

        .custom-notification h4 {
            margin: 0;
            font-weight: 600;
        }

        .custom-notification p {
            margin: 10px 0;
            font-size: 1rem;
        }

        .custom-notification .btn-close {
            background-color: #f44336; /* Rojo para el botón de cerrar */
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 1rem;
        }

        .custom-notification .btn-close:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

    <!-- Aquí va tu contenido del sitio -->
    <script>
        // Mostrar notificación de éxito
        window.onload = function() {
            // Crear la notificación
            const notification = document.createElement('div');
            notification.className = 'custom-notification';
            notification.innerHTML = `
                <h4>¡Gracias por tu compra!</h4>
                <p>Tu pedido ha sido procesado con éxito. Te esperamos pronto en Wislla Sin Fronteras.</p>
                <button class="btn-close" onclick="closeNotification()">Cerrar</button>
            `;
            document.body.appendChild(notification);

            // Redirigir después de 5 segundos
            setTimeout(function() {
                window.location.href = 'menu.php'; // Redirección automática después de 5 segundos
            }, 5000);
        }

        // Función para cerrar la notificación manualmente
        function closeNotification() {
            const notification = document.querySelector('.custom-notification');
            if (notification) {
                notification.remove();
            }
            window.location.href = 'menu.php'; // Redirección manual al menú después de cerrar la notificación
        }
    </script>

</body>
</html>
