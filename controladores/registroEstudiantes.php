<?php
session_start();
include_once "../conexion/bd.php";

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['errores'] = ["No tienes permisos para registrar estudiantes."];
    header("Location: ../registroEstudiantes.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre"], $_POST["telefono"], $_POST["apellido"], $_POST["curso"], $_POST["email"], $_POST["tutor_id"])){
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $apellido = trim($_POST["apellido"]);
    $curso = trim($_POST["curso"]);
    $telefono = trim($_POST["telefono"]);
    $tutor_id = trim($_POST["tutor_id"]);
    $errores = [];

    // ---------------- VALIDACIONES ----------------
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    } elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u', $nombre)) {
        $errores[] = "El nombre solo debe contener letras y espacios.";
    } elseif (strlen($nombre) > 100) {
        $errores[] = "El nombre no debe superar los 100 caracteres.";
    } elseif ($nombre !== strip_tags($nombre)) {
        $errores[] = 'No se permiten etiquetas HTML en el nombre';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $nombre)) {
        $errores[] = 'El nombre contiene contenido no permitido';
    }

    if (empty($apellido)) {
        $errores[] = "El apellido es obligatorio.";
    } elseif (strlen($apellido) > 100) {
        $errores[] = "El apellido no debe superar los 100 caracteres.";
    } elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u', $apellido)) {
        $errores[] = "El apellido solo debe contener letras y espacios.";
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

    
    if (empty($tutor_id) || !filter_var($tutor_id, FILTER_VALIDATE_INT)) {
        $errores[] = "El tutor es obligatorio e inválido.";
    }

    // Verificar duplicados
    if (empty($errores)) {
        try {
            $check_sql = $conexion->prepare("SELECT COUNT(*) FROM estudiantes WHERE email = :email OR telefono = :telefono ");
            $check_sql->bindParam(':email', $email, PDO::PARAM_STR);
            $check_sql->bindParam(':telefono', $telefono, PDO::PARAM_STR);
            $check_sql->execute();

            if ($check_sql->fetchColumn() > 0) {
                $errores[] = "El email o el telefono ya estan registrados.";
            }
        } catch (PDOException $e) {
            error_log("Error verificando duplicados: " . $e->getMessage());
            $errores[] = "Error interno. Intenta nuevamente.";
        }
    }


    // Si no hay errores de validacion se inserta el estudiante en la base de datos
    if(empty($errores)){
        $sql = $conexion->prepare("INSERT INTO estudiantes (nombre,apellido,curso,email, telefono,tutor_id) VALUES (:nombre,:apellido,:curso,:email, :telefono,:tutor_id)");
        $sql->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $sql->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $sql->bindParam(':curso', $curso, PDO::PARAM_STR);
        $sql->bindParam(':email', $email, PDO::PARAM_STR);
        $sql->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $sql->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
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
    $_SESSION['errores'] = "Error al registrar el estudiante";
    header("Location: ../registroEstudiantes.php");
    exit();
}
?>
