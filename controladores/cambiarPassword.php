<?php
session_start();
include_once '../conexion/bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password_actual']) && isset($_POST['nueva_password'])) {

    $password_actual = trim($_POST['password_actual']);
    $nueva_password = trim($_POST['nueva_password']);
    $id_usuario = $_SESSION['id_usuario'];

    $errores = [];

    // Validaciones
    if (empty($password_actual)) {
        $errores['password_actual'] = "La contraseña actual es obligatoria.";
    } elseif (strlen($password_actual) < 5) {
        $errores['password_actual'] = "La contraseña debe tener al menos 5 caracteres.";
    }

    if (empty($nueva_password)) {
        $errores['password_nueva'] = "La nueva contraseña es obligatoria.";
    } elseif (strlen($nueva_password) < 5) {
        $errores['password_nueva'] = "La nueva contraseña debe tener al menos 5 caracteres.";
    }

    if(!empty($password_actual) && !empty($nueva_password) && $password_actual === $nueva_password){
        $errores['password_nueva'] = "La nueva contraseña debe ser diferente a la actual.";
    }

    if (empty($errores)) {
        // Obtener la contraseña actual del usuario
        $sql = $conexion->prepare("SELECT password FROM usuarios WHERE id = :id_usuario");
        $sql->bindParam(':id_usuario', $id_usuario);
        $sql->execute();
        $usuario = $sql->fetch(PDO::FETCH_OBJ);

        if ($usuario && $password_actual === $usuario->password) {
            // Actualizar contraseña
            $sql = $conexion->prepare("UPDATE usuarios SET password = :nueva_password WHERE id = :id_usuario");
            $sql->bindParam(':nueva_password', $nueva_password);
            $sql->bindParam(':id_usuario', $id_usuario);
            $sql->execute();

            $_SESSION['exito'] = "Contraseña actualizada correctamente";
        } else {
            $_SESSION['errores'] = $errores;
        }

        header("Location: ../cambiarPassword.php");
        exit();
    } else {
        $_SESSION['errores'] = $errores;
        header("Location: ../cambiarPassword.php");
        exit();
    }

} else {
    echo "Error en la solicitud";
    exit();
}
?>
