<?php
session_start();
include_once("../conexion/bd.php");

// Verificar rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'tutor') {
    $_SESSION['errores'] = ["No tienes permisos para realizar esta acción. Solo tutores."];
    header("Location: ../index.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['incidente_id']) && isset($_POST['tutor_id']) && isset($_POST['comentario'])) {
    $incidente_id = trim($_POST['incidente_id']);
    $tutor_id = trim($_POST['tutor_id']);
    $comentario = trim($_POST['comentario']);
    $errores=[];

    // ---------------- VALIDACIONES ----------------
    
    if (!filter_var($incidente_id, FILTER_VALIDATE_INT)) {
        $errores[] = "ID de incidente inválido";
    }
    if (!filter_var($tutor_id, FILTER_VALIDATE_INT)) {
        $errores[] = "ID de tutor inválido";
    }
    
    if (empty($comentario)) {
        $errores[] = "El comentario no puede estar vacío.";
    }elseif ($comentario !== strip_tags($comentario)) {
        $errores[] = 'No se permiten etiquetas HTML en el comentario';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $comentario)) {
        $errores[] = 'El comentario contiene contenido no permitido';
    }

    // Validar existencia del incidente
    if (empty($errores)) {
        $checkIncidente = $conexion->prepare("SELECT COUNT(*) FROM incidentes WHERE id = :id");
        $checkIncidente->bindParam(':id', $incidente_id, PDO::PARAM_INT);
        $checkIncidente->execute();
        if ($checkIncidente->fetchColumn() == 0) {
            $errores[] = "El incidente no existe.";
        }
    }

    if (empty($errores)) {
        $sql = $conexion->prepare("INSERT INTO comentarios_incidente(incidente_id, tutor_id, comentario) VALUES(:incidente_id, :tutor_id, :comentario)");
        $sql->bindParam(':incidente_id', $incidente_id, PDO::PARAM_INT);
        $sql->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
        $sql->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        $sql->execute();
        $_SESSION['exito'] = "Comentario agregado correctamente";
        header("Location: ../incidentesMisEstudiantes.php");
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../seguimientoIncidente.php?id_incidente=" . $incidente_id);
    }
}else{
    $_SESSION['errores'] = ["Error al enviar el comentario."];
    header("Location: ../seguimientoIncidente.php?id_incidente=" . $incidente_id);
    exit();
}



?>