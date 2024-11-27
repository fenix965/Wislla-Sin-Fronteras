<?php
session_start();

if (!isset($_POST['index'], $_POST['cantidad']) || !is_numeric($_POST['cantidad']) || $_POST['cantidad'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
    exit;
}

$index = $_POST['index'];
$cantidad = (int) $_POST['cantidad'];

if (isset($_SESSION['carrito'][$index])) {
    $_SESSION['carrito'][$index]['cantidad'] = $cantidad;
    echo json_encode(['success' => true, 'carrito' => $_SESSION['carrito']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Índice no válido.']);
}