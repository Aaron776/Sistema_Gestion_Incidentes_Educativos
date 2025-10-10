<?php
session_start();
include_once("../conexion/bd.php");

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['errores'] = ["No tienes permisos para editar usuarios."];
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_incidente']) && isset($_POST['estado']) ){
    $id_incidente = trim($_POST['id_incidente']);
    $estado = trim($_POST['estado']);
    $errores=[];

    // Validaciones
    if (empty($id_incidente) || !ctype_digit($id_incidente)) {
        $errores[] = "El ID del incidente no es válido.";
    }

    if (empty($estado)) {
        $errores[] = "El estado del incidente es obligatorio.";
    } elseif (!in_array($estado, ['pendiente', 'investigacion', 'resuelto'])) { 
        $errores[] = "El estado ingresado no es válido.";
    }


    // Si no hay errores de validacion
    if(empty($errores)){
        $sql = $conexion->prepare("UPDATE incidentes SET estado=:estado WHERE id=:id_incidente");
        $sql->bindParam(':id_incidente', $id_incidente, PDO::PARAM_INT);
        $sql->bindParam(':estado', $estado, PDO::PARAM_STR);
        $sql->execute();

        $_SESSION['exito'] = "Estado del incidente actualizado correctamente";
        header("Location: ../gestionIncidentes.php");
    exit();
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../editarEstadoIncidente.php?id_incidente=$id_incidente");
        exit();
    }

}else{
    $_SESSION["errores"]=["Error en la solicitud"];
    header("Location: ../editarEstadoIncidente.php?id_incidente=$id_incidente");
    exit();
}
?>
