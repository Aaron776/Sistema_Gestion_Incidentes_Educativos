<?php
session_start();
include_once "../conexion/bd.php";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre"], $_POST["email"], $_POST["password"], $_POST["rol"])) {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $rol = trim($_POST["rol"]);
    $errores=[];

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

    if (empty($email)) {
        $errores[] = "El email es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }

    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria.";
    }

    if (empty($rol)) {
        $errores[] = "El rol es obligatorio.";
    }

    if(empty($errores)){
        $sql = $conexion->prepare("INSERT INTO usuarios (nombre,email,password,rol) VALUES (:nombre,:email,:password,:rol)");
        $sql->bindParam(':nombre', $nombre);
        $sql->bindParam(':email', $email);
        $sql->bindParam(':password', $password);
        $sql->bindParam(':rol', $rol);
        $sql->execute();

        $_SESSION['exito'] = "Usuario registrado correctamente";
        header("Location: ../gestionUsuarios.php");
        exit();
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../registroUsuarios.php");
        exit();
    }
}else{
    echo "Error en la solicitud";
}



?>