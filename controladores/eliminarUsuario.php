<?php
include_once '../conexion/bd.php';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_usuario'])){
    $id_usuario = $_POST['id_usuario'];
    $sql = $conexion->prepare("DELETE FROM usuarios WHERE id = :id_usuario");
    $sql->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    if($sql->execute()==true){
        header("Location: ../gestionUsuarios.php");
    }else{
        echo "Error al eliminar el usuario"; 
    }
}else{
    echo "Error en la solicitud";
}

?>