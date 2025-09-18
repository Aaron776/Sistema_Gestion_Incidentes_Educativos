<?php
session_start();
include_once("../conexion/bd.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_incidente']) && isset($_POST['estado']) ){
    $id_incidente = trim($_POST['id_incidente']);
    $estado = trim($_POST['estado']);
    $errores=[];

    // Validaciones
    if (empty($estado)) {
        $errores['estado'] = "El estado del incidente es obligatoria.";
    }


    // Si no hay errores de validacion
    if(empty($errores)){
        $sql = $conexion->prepare("UPDATE incidentes SET estado=:estado WHERE id=:id_incidente");
        $sql->bindParam(':id_incidente', $id_incidente, PDO::PARAM_INT);
        $sql->bindParam(':estado', $estado);
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
    echo "Error en la solicitud";
    exit();
}
?>
