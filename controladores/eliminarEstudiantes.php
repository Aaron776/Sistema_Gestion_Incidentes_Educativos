<?php
include_once '../conexion/bd.php';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_estudiante'])){
    $id_estudiante = $_POST['id_estudiante'];
    $sql = $conexion->prepare("DELETE FROM estudiantes WHERE id=:id_estudiante");
    $sql->bindParam(':id_estudiante', $id_estudiante);
    if($sql->execute()==true){
        header("Location: ../gestionEstudiantes.php");
    }else{
        echo "Error al eliminar el estudiante"; 
    }
}else{
    echo "Error en la solicitud";
}

?>