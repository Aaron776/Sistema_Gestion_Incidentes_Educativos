<?php
session_start();
include_once '../conexion/bd.php';

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin')) {
    $_SESSION['errores'] = ["No tienes permisos para editar estudiantes."];
    header("Location: ../index.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tutor_id']) && isset($_POST['id_estudiante']) && isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['telefono']) && isset($_POST['curso'])) {
    $id_estudiante = $_POST['id_estudiante'];
    $tutor_id = $_POST['tutor_id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $curso = $_POST['curso'];
    $errores=[];

    // ---------------- VALIDACIONES ----------------

    if (!filter_var($id_estudiante, FILTER_VALIDATE_INT)) {
        $errores[] = "ID de estudiante inválido.";
    }

    if (!filter_var($tutor_id, FILTER_VALIDATE_INT)) {
        $errores[] = "ID de tutor inválido.";
    }

    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    } elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u', $nombre)) {
        $errores[] = "El nombre solo debe contener letras y espacios.";
    } elseif (strlen($nombre) < 1) {
        $errores[] = "El nombre debe tener al menos 1 caracter.";
    } elseif (strlen($nombre) > 100) {
        $errores[] = "El nombre no debe superar los 100 caracteres.";
    } elseif ($nombre !== strip_tags($nombre)) {
        $errores[] = 'No se permiten etiquetas HTML en el nombre';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $nombre)) {
        $errores[] = 'El nombre contiene contenido no permitido';
    }

    if(empty($apellido)){
        $errores[] = "El apellido es obligatorio.";
    }elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u', $apellido)) {
        $errores[] = "El apellido solo debe contener letras y espacios.";
    }elseif (strlen($apellido) < 1) {
        $errores[] = "El apellido debe tener al menos 1 caracter.";
    }elseif (strlen($apellido) > 100) {
        $errores[] = "El apellido no debe superar los 100 caracteres.";
    }elseif ($apellido !== strip_tags($apellido)) {
        $errores[] = 'No se permiten etiquetas HTML en el apellido';
    }elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $apellido)) {
        $errores[] = 'El apellido contiene contenido no permitido';
    }

    if (empty($telefono)) {
        $errores[] = "El telefono es obligatorio.";
    } elseif (!preg_match('/^\d{10}$/', $telefono)) {
        $errores[] = "El telefono debe tener 10 digitos.";
    }

    if (empty($curso)) {
        $errores[] = "El curso es obligatorio.";
    }elseif ($curso !== strip_tags($curso)) {
        $errores[] = 'No se permiten etiquetas HTML en el curso';
    }elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $curso)) {
        $errores[] = 'El curso contiene contenido no permitido';
    }elseif (strlen($curso) < 1) {
        $errores[] = "El curso debe tener al menos 1 caracter.";
    }elseif (strlen($curso) > 100) {
        $errores[] = "El curso no debe superar los 100 caracteres.";
    }

    if (empty($email)) {
        $errores[] = "El email es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }

    if (empty($tutor_id)) {
        $errores[] = "El tutor es obligatorio.";
    }

    // Verificar existencia del estudiante
    $check = $conexion->prepare("SELECT id FROM estudiantes WHERE id = :id_estudiante");
    $check->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
    $check->execute();
    if ($check->rowCount() === 0) {
        $errores[] = "El estudiante no existe.";
    }


    if(empty($errores)){
        $sql = $conexion->prepare("UPDATE estudiantes SET tutor_id = :tutor_id, nombre = :nombre, apellido = :apellido, email = :email, telefono = :telefono, curso = :curso WHERE id = :id_estudiante");
        $sql->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
        $sql->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
        $sql->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $sql->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $sql->bindParam(':email', $email, PDO::PARAM_STR);
        $sql->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $sql->bindParam(':curso', $curso, PDO::PARAM_STR);
        $sql->execute();

        $_SESSION['exito'] = "Estudiante actualizado correctamente";
        header("Location: ../gestionEstudiantes.php");
        exit();
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../editarEstudiante.php?id_estudiante=$id_estudiante");
        exit();
    }
} else {
    $_SESSION['errores'] = ["Error al editar el estudiante"];
    header("Location: ../editarEstudiante.php?id_estudiante=$id_estudiante");
    exit();
}
?>

