<?php
session_start();
include_once("../conexion/bd.php");

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'tutor') {
    $_SESSION['errores'] = ["No tienes permisos para editar usuarios."];
    header("Location: ../index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incidente_id']) && isset($_POST['comentario_id']) && isset($_POST['comentario'])){
    $id_incidente = $_POST['incidente_id'];
    $id_comentario = $_POST['comentario_id'];
    $comentario = $_POST['comentario'];
    $errores=[];

    ///-------VALIDACIONES-------////
    if (empty($comentario)) {
        $errores[] = "El comentario es obligatorio.";
    } elseif (strlen($comentario) > 500) {
        $errores[] = "El comentario no debe superar los 500 caracteres.";
    } elseif ($comentario !== strip_tags($comentario)) {
        $errores[] = "El comentario no debe contener etiquetas HTML.";
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $comentario)) {
        $errores[] = "El comentario contiene contenido inapropiado.";
    }

    if (empty($id_incidente) || !ctype_digit($id_incidente)) {
        $errores[] = "El ID del incidente no es válido.";
    }

    if (empty($id_comentario) || !ctype_digit($id_comentario)) {
        $errores[] = "El ID del comentario no es válido.";
    }


    // Si no hay errores de validacion
    if(empty($errores)){
        
        // Verificar que el comentario exista y pertenezca al incidente correcto
        $verificar = $conexion->prepare("SELECT id FROM comentarios_incidente WHERE id = :id_comentario AND incidente_id = :id_incidente");
        $verificar->bindParam(':id_comentario', $id_comentario, PDO::PARAM_INT);
        $verificar->bindParam(':id_incidente', $id_incidente, PDO::PARAM_INT);
        $verificar->execute();

        if ($verificar->rowCount() === 0) {
            $_SESSION['errores'] = ["El comentario no existe o no pertenece al incidente especificado."];
            header("Location: ../seguimientoIncidente.php?id_incidente=$id_incidente");
            exit();
        }

        // Actualizar el comentario
        $sql = $conexion->prepare("UPDATE comentarios_incidente SET comentario=:comentario WHERE id=:id_comentario");
        $sql->bindParam(':id_comentario', $id_comentario, PDO::PARAM_INT);
        $sql->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        $sql->execute();
        $_SESSION['exito'] = "Comentario actualizado correctamente";
        header("Location: ../seguimientoIncidente.php?id_incidente=$id_incidente");
        exit();
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../editarComentarioSeguimiento.php?id_incidente=$id_incidente&id_comentario=$id_comentario");
        exit();
    }

}else{
    $_SESSION['errores'] = ["Error al enviar el comentario."];
    header("Location: ../editarComentarioSeguimiento.php?id_incidente=$id_incidente&id_comentario=$id_comentario");
    exit();   
}




?>