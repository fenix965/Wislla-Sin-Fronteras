<?php
session_start();
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index'])) {
    $index = (int) $_POST['index'];
 
    if (isset($_SESSION['carrito'][$index])) {
        unset($_SESSION['carrito'][$index]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reorganizar índices
 
        echo json_encode([
            'success' => true,
            'message' => 'Elemento eliminado correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Índice no válido'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se recibió un índice válido'
    ]);
}
 