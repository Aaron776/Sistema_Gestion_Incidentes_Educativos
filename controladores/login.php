<?php
session_start();
include_once '../conexion/bd.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    //Buscar usuario en la base de datos
    $stmt = $conexion->prepare("SELECT id,nombre,email,rol FROM usuarios WHERE email = :email AND password = :password");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if($user==true){
        $_SESSION['id_usuario'] = $user->id;
        $_SESSION['email'] = $user->email;
        $_SESSION['nombre'] = $user->nombre;
        $_SESSION['rol'] = $user->rol;
        $_SESSION['logueado'] = true;
        

        switch ($_SESSION['rol']) {
            case 'admin':
                header("Location: ../dash_admin.php");
                exit();
                break;
                
            case 'docente':
                header("Location: ../dash_docente.php");
                exit();
                break;
                
            case 'tutor':
                header("Location: ../dash_tutor.php");
                exit();
                break;
                
            default:
                header("Location: ../index.php");
                exit();
                break;
        }
    }else{
        echo "El usuario no existe";
    }
}



?>