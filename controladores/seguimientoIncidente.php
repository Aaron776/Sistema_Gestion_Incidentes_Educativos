<?php
include_once("../conexion/bd.php");

if(isset($_POST['incidente_id']) && isset($_POST['tutor_id']) && isset($_POST['comentario'])) {
    $incidente_id = $_POST['incidente_id'];
    $tutor_id = $_POST['tutor_id'];
    $comentario = $_POST['comentario'];

    $sql=$conexion->prepare("INSERT INTO comentarios_incidente(incidente_id, tutor_id, comentario) VALUES(:incidente_id, :tutor_id, :comentario)");
    $sql->bindParam(':incidente_id', $incidente_id);
    $sql->bindParam(':tutor_id', $tutor_id);
    $sql->bindParam(':comentario', $comentario);

    if($sql->execute()){
        header("Location: ../seguimientoIncidente.php?id_incidente=" . $incidente_id);
        exit();
    }else{
        echo "Error al publicar el comentario";
    }
}else{
    echo "Error en la solicitud";
}



?>