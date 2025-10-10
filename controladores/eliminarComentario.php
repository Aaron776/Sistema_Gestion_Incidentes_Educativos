<?php
session_start();
include_once("../conexion/bd.php");
$errores = [];

// -------- Verificar rol --------
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'tutor') {
    $_SESSION['errores'] = ["No tienes permisos para eliminar comentarios."];
    header("Location: ../index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario_id']) && isset($_GET['id_incidente'])){
    $id_incidente = $_GET['id_incidente'];
    $comentario_id = $_POST['comentario_id'];

    //----- Validaciones -----//
    if (!filter_var($id_incidente, FILTER_VALIDATE_INT)) {
        $errores[] = "El ID del incidente no es válido.";
    }
    if (!filter_var($comentario_id, FILTER_VALIDATE_INT)) {
        $errores[] = "El ID del comentario no es válido.";
    }

    if(empty($errores)){
        // Verificar que el comentario pertenezca al tutor logueado
        $check = $conexion->prepare("SELECT tutor_id FROM comentarios_incidente WHERE id = :id");
        $check->bindParam(':id', $comentario_id, PDO::PARAM_INT);
        $check->execute();
        $comentario = $check->fetch(PDO::FETCH_OBJ);

        if (!$comentario) {
            $errores[] = "El comentario no existe.";
        } elseif ($comentario->tutor_id != $_SESSION['id_usuario']) {
            $errores[] = "No tienes permisos para eliminar este comentario.";
        }

        // Eliminar el comentario
        $sql = $conexion->prepare("DELETE FROM comentarios_incidente WHERE id = :comentario_id");
        $sql->bindParam(':comentario_id', $comentario_id, PDO::PARAM_INT);
        $sql->execute();

        $_SESSION['exito'] = "Comentario eliminado correctamente";
        header("Location: ../seguimientoIncidente.php?id_incidente=" . $id_incidente);
        exit();
    
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../seguimientoIncidente.php?id_incidente=" . $id_incidente);
        exit();
    }
    
}else{
    $_SESSION['errores'] = ["Solicitud inválida o parámetros faltantes."];
    header("Location: ../seguimientoIncidente.php?id_incidente=" . $id_incidente);
    exit();
}



?>