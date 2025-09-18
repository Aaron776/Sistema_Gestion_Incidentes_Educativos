<?php
include("conexion/bd.php");
session_start();

// Solo admin puede obtener notificaciones
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode([]);
    exit();
}

// Obtener los últimos 5 incidentes (ordenados por fecha descendente)
$sql = $conexion->prepare("SELECT id, mensaje, fecha, leida FROM notificaciones ORDER BY fecha DESC LIMIT 5");
$sql->execute();
$notificaciones = $sql->fetchAll(PDO::FETCH_ASSOC);

// Convertir booleanos a true/false explícitamente para JS
foreach ($notificaciones as &$notif) {
    $notif['leida'] = (bool)$notif['leida'];
}

// Devolver JSON
header('Content-Type: application/json');
echo json_encode($notificaciones);
exit();
?>
