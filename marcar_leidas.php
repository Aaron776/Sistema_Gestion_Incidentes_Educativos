<?php
session_start();
include_once("conexion/bd.php");

// Solo admin puede marcar notificaciones como leídas
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

try {
    $sql = $conexion->prepare("UPDATE notificaciones SET leida = TRUE WHERE leida = FALSE");
    $sql->execute();
    $afectadas = $sql->rowCount();

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'actualizadas' => $afectadas]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
