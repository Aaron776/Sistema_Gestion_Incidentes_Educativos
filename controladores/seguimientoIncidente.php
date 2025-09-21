<?php
session_start();
include_once("../conexion/bd.php");

if(isset($_POST['incidente_id']) && isset($_POST['tutor_id']) && isset($_POST['comentario'])) {
    $incidente_id = $_POST['incidente_id'];
    $tutor_id = $_POST['tutor_id'];
    $comentario = $_POST['comentario'];
    $errores=[];

    // ---------------- VALIDACIONES ----------------
    if (empty($comentario)) {
        $errores[] = "El comentario no puede estar vacío.";
    }elseif ($comentario !== strip_tags($comentario)) {
        $errores[] = 'No se permiten etiquetas HTML en el comentario';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $comentario)) {
        $errores[] = 'El comentario contiene contenido no permitido';
    }

    if (empty($errores)) {
        $sql = $conexion->prepare("INSERT INTO comentarios_incidente(incidente_id, tutor_id, comentario) VALUES(:incidente_id, :tutor_id, :comentario)");
        $sql->bindParam(':incidente_id', $incidente_id);
        $sql->bindParam(':tutor_id', $tutor_id);
        $sql->bindParam(':comentario', $comentario);
        $sql->execute();
        $_SESSION['exito'] = "Comentario agregado correctamente";
        header("Location: ../incidentesMisEstudiantes.php");
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../seguimientoIncidente.php?id_incidente=" . $incidente_id);
    }
}else{
    echo "Error en la solicitud";
}



?>