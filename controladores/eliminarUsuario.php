<?php
session_start();
include_once '../conexion/bd.php';
$errores = [];

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['errores'] = ["No tienes permisos para eliminar usuarios."];
    header("Location: ../gestionUsuarios.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];

    // ---------------- VALIDACIONES ----------------
    if (empty($id_usuario)) {
        $errores[] = "El ID del usuario es obligatorio.";
    } elseif (!filter_var($id_usuario, FILTER_VALIDATE_INT)) {
        $errores[] = "ID de usuario inválido.";
    } elseif ($id_usuario == $_SESSION['id_usuario']) {
        $errores[] = "No puedes eliminar tu propio usuario.";
    }

    // Verificar existencia del usuario
    if (empty($errores)) {
        $check = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE id = :id_usuario");
        $check->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $check->execute();

        if ($check->fetchColumn() == 0) {
            $errores[] = "El usuario no existe.";
        }
    }

    // ---------------- ELIMINACIÓN ----------------
    if (empty($errores)) {
        $sql = $conexion->prepare("DELETE FROM usuarios WHERE id = :id_usuario");
        $sql->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $sql->execute();

        $_SESSION['exito'] = "Usuario eliminado correctamente";
    } else {
        $_SESSION['errores'] = $errores;
    }

    header("Location: ../gestionUsuarios.php");
    exit();

} else {
    $_SESSION['errores'] = ["Error al eliminar el usuario"];
    header("Location: ../gestionUsuarios.php");
    exit();
}
?>
