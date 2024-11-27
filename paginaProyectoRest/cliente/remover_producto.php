<?php
session_start();

if (!isset($_POST['index']) || !isset($_SESSION['carrito'][$_POST['index']])) {
    echo json_encode(['success' => false, 'message' => 'Índice no válido.']);
    exit;
}

unset($_SESSION['carrito'][$_POST['index']]);
$_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar

echo json_encode(['success' => true]);