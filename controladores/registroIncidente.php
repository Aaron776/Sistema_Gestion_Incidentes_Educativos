<?php
session_start();
include_once '../conexion/bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tipo'], $_POST['estudiante'], $_POST['descripcion'], $_POST['usuario_reporta'], $_POST['lugar'], $_POST['fecha_incidente'], $_POST['hora_incidente'])) {

    $tipo = trim($_POST['tipo']);
    $estudiante = trim($_POST['estudiante']);
    $descripcion = trim($_POST['descripcion']);
    $usuario_reporta = trim($_POST['usuario_reporta']);
    $lugar = trim($_POST['lugar']);
    $fecha_incidente = trim($_POST['fecha_incidente']);
    $hora_incidente = trim($_POST['hora_incidente']);
    $errores=[];

    // ---------------- VALIDACIONES ----------------
    if (empty($tipo)) {
        $errores[] = "El tipo es obligatorio.";
    }

    if (empty($estudiante)) {
        $errores[] = "El estudiante es obligatorio.";
    }

    if (empty($descripcion)) {
        $errores[] = "La descripción es obligatoria.";
    }elseif ($descripcion !== strip_tags($descripcion)) {
        $errores[] = 'No se permiten etiquetas HTML en el nombre';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $descripcion)) {
        $errores[] = 'La descripción contiene contenido no permitido';
    }


    if (empty($lugar)) {
        $errores[] = "El lugar es obligatorio.";
    }elseif ($lugar !== strip_tags($lugar)) {
        $errores[] = 'No se permiten etiquetas HTML en el lugar';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $lugar)) {
        $errores[] = 'El lugar contiene contenido no permitido';
    }

    if (empty($fecha_incidente)) {
        $errores[] = "La fecha es obligatoria.";
    }

    if (empty($hora_incidente)) {
        $errores[] = "La hora es obligatoria.";
    }elseif ($hora_incidente !== strip_tags($hora_incidente)) {
        $errores[] = 'No se permiten etiquetas HTML en la hora';
    }

    // Manejo del archivo (si se subió)
    $archivo_incidente = null;
    if (isset($_FILES['archivo_incidente']) && $_FILES['archivo_incidente']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = time() . '_' . $_FILES['archivo_incidente']['name'];
        $rutaDestino = '../reportes/' . $nombreArchivo;
        if (move_uploaded_file($_FILES['archivo_incidente']['tmp_name'], $rutaDestino)) {
            $archivo_incidente = $nombreArchivo;
        }
    }

    // Si no hay errores en la validación se guarda el incidente en la base de datos
    if(empty($errores)){
        try {
            // Iniciar transacción
            $conexion->beginTransaction();
    
            // Insertar incidente
            $sql = $conexion->prepare(" INSERT INTO incidentes(estudiante_id, usuario_reporta_id, fecha_incidente, hora_incidente, lugar, tipo, descripcion, archivo_incidente) VALUES (:estudiante_id, :usuario_reporta_id, :fecha_incidente, :hora_incidente, :lugar, :tipo, :descripcion, :archivo_incidente) RETURNING id");
            $sql->bindParam(':estudiante_id', $estudiante);
            $sql->bindParam(':usuario_reporta_id', $usuario_reporta);
            $sql->bindParam(':tipo', $tipo);
            $sql->bindParam(':descripcion', $descripcion);
            $sql->bindParam(':lugar', $lugar);
            $sql->bindParam(':fecha_incidente', $fecha_incidente);
            $sql->bindParam(':hora_incidente', $hora_incidente);
            $sql->bindParam(':archivo_incidente', $archivo_incidente);
    
            $sql->execute();
            $incidente_id = $sql->fetchColumn(); // ID del incidente insertado
    
            // Obtener nombre y apellido del estudiante
            $sql_est = $conexion->prepare("SELECT nombre, apellido FROM estudiantes WHERE id = :id");
            $sql_est->bindParam(':id', $estudiante);
            $sql_est->execute();
            $estudianteData = $sql_est->fetch(PDO::FETCH_OBJ);
    
            $mensaje = "Nuevo incidente registrado por " . $estudianteData->nombre . " " . $estudianteData->apellido;
    
            // Insertar notificación
            $sql_notif = $conexion->prepare("INSERT INTO notificaciones (mensaje, incidente_id, leida, fecha)VALUES (:mensaje, :incidente_id, FALSE, NOW())");
            $sql_notif->bindParam(':mensaje', $mensaje);
            $sql_notif->bindParam(':incidente_id', $incidente_id);
            $sql_notif->execute();
    
            // Confirmar transacción
            $conexion->commit();
    
            $_SESSION['exito'] = "Incidente registrado correctamente";
            header("Location: ../incidentesReportados.php");
            exit();
    
        } catch (PDOException $e) {
            $conexion->rollBack();
            echo "Error al registrar el incidente: " . $e->getMessage();
        }  
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: ../registroIncidentes.php");
        exit();
    }

    

} else {
    echo "Error en la solicitud";
}
?>
