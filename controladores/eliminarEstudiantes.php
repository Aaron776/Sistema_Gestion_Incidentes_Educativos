<?php
session_start();
include_once '../conexion/bd.php';
$errores = [];

// Verificar rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    $errores[] = "No tienes permisos para eliminar estudiantes.";
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_estudiante'])){
    $id_estudiante = $_POST['id_estudiante'];

    //------- Validaciones -------
    if (empty($id_estudiante)) {
        $errores[] = "El ID del estudiante es obligatorio.";
    } elseif (!filter_var($id_estudiante, FILTER_VALIDATE_INT)) {
        $errores[] = "ID de estudiante inválido.";
    }

    // Verificar existencia
    if (empty($errores)) {
        $check = $conexion->prepare("SELECT COUNT(*) FROM estudiantes WHERE id = :id_estudiante");
        $check->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
        $check->execute();
        if ($check->fetchColumn() == 0) {
            $errores[] = "El estudiante no existe.";
        }
    }
    
    
    if(empty($errores)){
        $sql = $conexion->prepare("DELETE FROM estudiantes WHERE id=:id_estudiante");
        $sql->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
        $sql->execute();

        $_SESSION['exito'] = "Estudiante eliminado correctamente";
        header("Location: ../gestionEstudiantes.php");
        exit();
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../gestionEstudiantes.php");
        exit();
    }
}else{
    $_SESSION['errores'] = "Error al eliminar el estudiante";
    header("Location: ../gestionEstudiantes.php");
    exit();
}

?>