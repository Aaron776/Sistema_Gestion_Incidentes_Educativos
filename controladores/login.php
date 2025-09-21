<?php
session_start();
include_once '../conexion/bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $errores = [];

    // ---------------- VALIDACIONES ----------------
    if (empty($email)) {
        $errores[] = "El email no puede estar vacío.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }
    if (empty($password)) {
        $errores[] = "La contraseña no puede estar vacía.";
    }

    if (empty($errores)) {
        // Buscar usuario en la base de datos
        $stmt = $conexion->prepare("SELECT id,nombre,email,rol,password FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user && password_verify($password, $user->password)) {
            // Login correcto
            $_SESSION['id_usuario'] = $user->id;
            $_SESSION['email'] = $user->email;
            $_SESSION['nombre'] = $user->nombre;
            $_SESSION['rol'] = $user->rol;
            $_SESSION['logueado'] = true;

            switch ($user->rol) {
                case 'admin':
                    header("Location: ../dash_admin.php");
                    exit();
                case 'docente':
                    header("Location: ../dash_docente.php");
                    exit();
                case 'tutor':
                    header("Location: ../dash_tutor.php");
                    exit();
                default:
                    header("Location: ../index.php");
                    exit();
            }
        } else {
            // Credenciales incorrectas
            $errores[] = "Email o contraseña incorrectos.";
            $_SESSION['errores'] = $errores;
            header("Location: ../index.php");
            exit();
        }
    } else {
        // Errores de validación
        $_SESSION['errores'] = $errores;
        header("Location: ../index.php");
        exit();
    }
} else {
    echo "Error en la solicitud";
}
?>
