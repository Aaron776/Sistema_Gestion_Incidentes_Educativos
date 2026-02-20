<?php
session_start();
include_once("conexion/bd.php");

// Solo admin puede obtener notificaciones
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode([]);
    exit();
}

// Parámetro: si se pide todas (al abrir el panel) o solo las no leídas (para el conteo del badge)
$soloNoLeidas = isset($_GET['solo_no_leidas']) && $_GET['solo_no_leidas'] === '1';

$whereClause = $soloNoLeidas ? "WHERE leida = FALSE" : "";

$sql = $conexion->prepare("
    SELECT 
        id, 
        mensaje, 
        fecha, 
        leida 
    FROM notificaciones 
    {$whereClause}
    ORDER BY id DESC 
    LIMIT 20
");
$sql->execute();
$notificaciones = $sql->fetchAll(PDO::FETCH_ASSOC);

// Formatear salida
foreach ($notificaciones as &$notif) {
    $notif['leida'] = (bool) $notif['leida'];
    $notif['fecha'] = date('d/m/Y H:i', strtotime($notif['fecha']));
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($notificaciones);
exit;
