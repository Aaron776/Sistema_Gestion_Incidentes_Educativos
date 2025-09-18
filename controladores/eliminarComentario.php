<?php
include_once("../conexion/bd.php");

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario_id']) && isset($_GET['id_incidente'])){
    $id_incidente = $_GET['id_incidente'];
    $comentario_id = $_POST['comentario_id'];
    $sql = $conexion->prepare("DELETE FROM comentarios_incidente WHERE id = :comentario_id");
    $sql->bindParam(':comentario_id', $comentario_id);

    if($sql->execute()){
       header("Location: ../seguimientoIncidente.php?id_incidente=" . $id_incidente);
    }else{
        echo "Error al eliminar el comentario";
    }
    
}else{
    echo "Error en la solicitud";
}



?>