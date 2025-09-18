<?php
session_start();
include_once "../conexion/bd.php";
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre"], $_POST["telefono"], $_POST["apellido"], $_POST["curso"], $_POST["email"], $_POST["tutor_id"])){
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $apellido = trim($_POST["apellido"]);
    $curso = trim($_POST["curso"]);
    $telefono = trim($_POST["telefono"]);
    $tutor_id = trim($_POST["tutor_id"]);
    $errores = [];

    // ---------------- VALIDACIONES ----------------
    if (strlen($nombre) > 100) {
        $errores[] = "El nombre no debe superar los 100 caracteres.";
    } elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u', $nombre)) {
        $errores[] = "El nombre solo debe contener letras y espacios.";
    } elseif (strlen($nombre) < 1) {
        $errores[] = "El nombre debe tener al menos 1 caracter.";
    } elseif (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    } elseif ($nombre !== strip_tags($nombre)) {
        $errores[] = 'No se permiten etiquetas HTML en el nombre';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $nombre)) {
        $errores[] = 'El nombre contiene contenido no permitido';
    }

    if (strlen($apellido) > 100) {
        $errores[] = "El apellido no debe superar los 100 caracteres.";
    } elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u', $apellido)) {
        $errores[] = "El apellido solo debe contener letras y espacios.";
    } elseif (strlen($apellido) < 1) {
        $errores[] = "El apellido debe tener al menos 1caracter.";
    } elseif (empty($apellido)) {
        $errores[] = "El apellido es obligatorio.";
    } elseif ($apellido !== strip_tags($apellido)) {
        $errores[] = 'No se permiten etiquetas HTML en el apellido';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $apellido)) {
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


    // Si no hay errores de validacion se inserta el estudiante en la base de datos
    if(empty($errores)){
        $sql = $conexion->prepare("INSERT INTO estudiantes (nombre,apellido,curso,email, telefono,tutor_id) VALUES (:nombre,:apellido,:curso,:email, :telefono,:tutor_id)");
        $sql->bindParam(':nombre', $nombre);
        $sql->bindParam(':apellido', $apellido);
        $sql->bindParam(':curso', $curso);
        $sql->bindParam(':email', $email);
        $sql->bindParam(':telefono', $telefono);
        $sql->bindParam(':tutor_id', $tutor_id);
        $sql->execute();

        $_SESSION['exito'] = "Estudiante registrado correctamente";
        header("Location: ../gestionEstudiantes.php");
        exit();
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../registroEstudiantes.php");
        exit();
    }
    

}else{
    echo "Error en la solicitud";
}
?>
