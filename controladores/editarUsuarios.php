<?php
session_start();
include_once '../conexion/bd.php';

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['errores'] = ["No tienes permisos para editar usuarios."];
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_usuario']) && isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['rol'])) {
    $id_usuario = trim($_POST['id_usuario']);
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $rol = trim($_POST['rol']);
    $errores=[];
    

    // ---------------- VALIDACIONES ----------------

    if (!filter_var($id_usuario, FILTER_VALIDATE_INT)) {
        $errores[] = "ID de usuario inválido.";
    }

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
    } elseif (strlen($email) > 150) {
        $errores[] = "El email no debe superar los 150 caracteres.";
    }

    if (empty($rol)) {
        $errores[] = "El rol es obligatorio.";
    }

    // Verificar si el usuario existe
    if (empty($errores)) {
        $check = $conexion->prepare("SELECT id FROM usuarios WHERE id = :id_usuario");
        $check->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $check->execute();

        if ($check->rowCount() === 0) {
            $errores[] = "El usuario no existe.";
        }
    }

    // Si no hay errores en la validación se actualiza el usuario
    if(empty($errores)){
        $sql = $conexion->prepare("UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol WHERE id = :id_usuario");
        $sql->bindParam(':id_usuario', $id_usuario);
        $sql->bindParam(':nombre', $nombre);
        $sql->bindParam(':email', $email);
        $sql->bindParam(':rol', $rol);
        $sql->execute();

        $_SESSION['exito'] = "Usuario actualizado correctamente";
        header("Location: ../gestionUsuarios.php");
        exit();
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../editarUsuario.php?id_usuario=$id_usuario");
        exit();
    }

} else {
    $_SESSION['errores'] = ["Error al editar el usuario."];
    header("Location: ../gestionUsuarios.php");
    exit();
}


?>