<?php
session_start();

// Verifica si la sesión del carrito existe, si no, la inicializa
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Verifica si se envían los datos requeridos
if (isset($_POST['id'], $_POST['nombre'], $_POST['precio'], $_POST['imagen'])) {
    // Sanitiza y valida los datos de entrada
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $imagen = trim($_POST['imagen']);

    // Verifica que los campos no estén vacíos o sean inválidos
    if ($id <= 0 || empty($nombre) || $precio <= 0 || empty($imagen)) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        exit;
    }

    $existe = false;
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['id'] == $id) {
            // Si el producto ya existe, puedes incrementar la cantidad o simplemente no hacer nada
            $item['cantidad'] = isset($item['cantidad']) ? $item['cantidad'] + 1 : 2; // Incrementa la cantidad
            $existe = true;
            break;
        }
    }

    // Si no existe, agrega un nuevo producto al carrito
    if (!$existe) {
        $_SESSION['carrito'][] = [
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'imagen' => $imagen,
            'cantidad' => 1 // Asigna una cantidad inicial de 1
        ];
    }

    // Retorna el estado de éxito con el carrito actualizado
    echo json_encode(['success' => true, 'message' => 'Producto añadido al carrito', 'carrito' => $_SESSION['carrito']]);
} else {
    // Retorna un mensaje de error si faltan datos
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}
?>
