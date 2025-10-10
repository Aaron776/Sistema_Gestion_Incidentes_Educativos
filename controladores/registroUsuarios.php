<?php
session_start();
include_once "../conexion/bd.php";


if ($_SESSION['rol'] !== 'admin') {
    $_SESSION['errores'] = ["No tienes permisos para registrar usuarios. Solo administradores."];
    header("Location: ../index.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre"], $_POST["email"], $_POST["password"], $_POST["rol"])) {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $rol = trim($_POST["rol"]);
    $errores=[];

    // ---------------- VALIDACIONES ----------------
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    } elseif (strlen($nombre) > 100) {
        $errores[] = "El nombre no debe superar los 100 caracteres.";
    } elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u', $nombre)) {
        $errores[] = "El nombre solo debe contener letras y espacios.";
    } elseif ($nombre !== strip_tags($nombre)) {
        $errores[] = 'No se permiten etiquetas HTML en el nombre';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $nombre)) {
        $errores[] = 'El nombre contiene contenido no permitido';
    }

    if (empty($email)) {
        $errores[] = "El email es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errores[] = "El formato del email no es válido.";
    }

    
    if (empty($password)) {
        $errores[] = "La contraseña es obligatoria.";
    } elseif (strlen($password) < 5) {
        $errores[] = "La contraseña debe tener al menos 5 caracteres.";
    } elseif (strlen($password) > 255) {
        $errores[] = "La contraseña es demasiado larga.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errores[] = "La contraseña debe contener al menos una letra mayúscula.";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errores[] = "La contraseña debe contener al menos una letra minúscula.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errores[] = "La contraseña debe contener al menos un número.";
    } elseif (preg_match('/\s/', $password)) {
        $errores[] = "La contraseña no debe contener espacios.";
    }

    $roles_permitidos = ['admin', 'tutor', 'docente']; 
    if (empty($rol)) {
        $errores[] = "El rol es obligatorio.";
    } elseif (!in_array($rol, $roles_permitidos, true)) {
        $errores[] = "El rol seleccionado no es válido.";
    }

    // Verificar duplicados
    if (empty($errores)) {
        try {
            $duplicado = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
            $duplicado->bindParam(':email', $email, PDO::PARAM_STR);
            $duplicado->execute();

            if ($duplicado->fetchColumn() > 0) {
                $errores[] = "El email ya está registrado.";
            }
        } catch (PDOException $e) {
            error_log("Error verificando duplicados: " . $e->getMessage());
            $errores[] = "Error interno. Intenta nuevamente.";
        }
    }

    if(empty($errores)){
        $password_ecriptada = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = $conexion->prepare("INSERT INTO usuarios (nombre,email,password,rol) VALUES (:nombre,:email,:password,:rol)");
        $sql->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $sql->bindParam(':email', $email, PDO::PARAM_STR);
        $sql->bindParam(':password', $password_ecriptada, PDO::PARAM_STR);
        $sql->bindParam(':rol', $rol, PDO::PARAM_STR);
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
    $_SESSION['errores'] = ["Solicitud inválida"];
    header("Location: ../registroUsuarios.php");
    exit();
}



?>