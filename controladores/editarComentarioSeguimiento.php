<?php
include_once("../conexion/bd.php");

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incidente_id']) && isset($_POST['comentario_id']) && isset($_POST['comentario'])){
    $id_incidente = $_POST['incidente_id'];
    $id_comentario = $_POST['comentario_id'];
    $comentario = $_POST['comentario'];

    $sql=$conexion->prepare("UPDATE comentarios_incidente SET comentario = :comentario WHERE id = :id_comentario");
    $sql->bindParam(':comentario', $comentario);
    $sql->bindParam(':id_comentario', $id_comentario);

    if ($sql->execute()===true){
        header("Location: ../seguimientoIncidente.php?id_incidente=$id_incidente");
        exit();
    } else {
        echo "Error al editar el comentario";
    }

}else{
    echo "Error en la solicitud";
}




?>