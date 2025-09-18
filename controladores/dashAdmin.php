<?php
include_once 'conexion/bd.php';

// Obtener cantidad de incidentes totales
$sql=$conexion->prepare("SELECT count(*) AS total_incidentes FROM incidentes");
$sql->execute();
$cantidadIncidentes = $sql->fetch(PDO::FETCH_OBJ);

// Obtener los ultimos 5 incidentes
$sql=$conexion->prepare("SELECT incidentes.descripcion AS descripcion, estudiantes.nombre AS nombre_estudiante, estudiantes.apellido AS apellido_estudiante FROM incidentes INNER JOIN estudiantes ON incidentes.estudiante_id = estudiantes.id ORDER BY incidentes.id DESC LIMIT 5");
$sql->execute();
$ultimosIncidentes = $sql->fetchAll(PDO::FETCH_OBJ);

// Obtener cantidad de incidentes pendientes
$sql=$conexion->prepare("SELECT count(*) AS total_pendientes FROM incidentes WHERE estado = 'pendiente'");
$sql->execute();
$cantidadPendientes = $sql->fetch(PDO::FETCH_OBJ);

// Obtener cantidad de incidentes resueltos
$sql=$conexion->prepare("SELECT count(*) AS total_resueltos FROM incidentes WHERE estado = 'resuelto'");
$sql->execute();
$cantidadResueltos = $sql->fetch(PDO::FETCH_OBJ);

// Obtener cantidad de incidentes en investigacion
$sql=$conexion->prepare("SELECT count(*) AS total_investigacion FROM incidentes WHERE estado = 'investigacion'");
$sql->execute();
$cantidadInvestigacion = $sql->fetch(PDO::FETCH_OBJ);
?>