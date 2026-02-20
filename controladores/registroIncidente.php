<?php
session_start();
include_once '../conexion/bd.php';


// Verificar rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'docente') {
    $_SESSION['errores'] = ["No tienes permisos para realizar esta acción. Solo tutores."];
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tipo'], $_POST['estudiante'], $_POST['descripcion'], $_POST['usuario_reporta'], $_POST['lugar'], $_POST['fecha_incidente'], $_POST['hora_incidente'])) {

    $tipo = trim($_POST['tipo']);
    $estudiante = trim($_POST['estudiante']);
    $descripcion = trim($_POST['descripcion']);
    $usuario_reporta = trim($_POST['usuario_reporta']);
    $lugar = trim($_POST['lugar']);
    $fecha_incidente = trim($_POST['fecha_incidente']);
    $hora_incidente = trim($_POST['hora_incidente']);
    $errores = [];

    // ---------------- VALIDACIONES ----------------
    if (empty($tipo)) {
        $errores[] = "El tipo es obligatorio.";
    }

    if (!filter_var($estudiante, FILTER_VALIDATE_INT)) $errores[] = "Estudiante inválido.";
    if (!filter_var($usuario_reporta, FILTER_VALIDATE_INT)) $errores[] = "Usuario que reporta inválido.";

    if (empty($estudiante)) {
        $errores[] = "El estudiante es obligatorio.";
    }

    if (empty($descripcion)) {
        $errores[] = "La descripción es obligatoria.";
    } elseif ($descripcion !== strip_tags($descripcion)) {
        $errores[] = 'No se permiten etiquetas HTML en el nombre';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $descripcion)) {
        $errores[] = 'La descripción contiene contenido no permitido';
    }


    if (empty($lugar)) {
        $errores[] = "El lugar es obligatorio.";
    } elseif ($lugar !== strip_tags($lugar)) {
        $errores[] = 'No se permiten etiquetas HTML en el lugar.';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $lugar)) {
        $errores[] = 'El lugar contiene contenido no permitido.';
    } elseif (strlen($lugar) > 255) {
        $errores[] = "El lugar no puede superar los 255 caracteres.";
    }

    if (!DateTime::createFromFormat('Y-m-d', $fecha_incidente)) {
        $errores[] = "Formato de fecha inválido.";
    }

    if (!DateTime::createFromFormat('H:i', $hora_incidente)) {
        $errores[] = "Formato de hora inválido.";
    }

    if (empty($fecha_incidente)) {
        $errores[] = "La fecha es obligatoria.";
    }

    if (empty($hora_incidente)) {
        $errores[] = "La hora es obligatoria.";
    } elseif ($hora_incidente !== strip_tags($hora_incidente)) {
        $errores[] = 'No se permiten etiquetas HTML en la hora';
    }

    // Validar estudiante
    $checkEstudiante = $conexion->prepare("SELECT COUNT(*) FROM estudiantes WHERE id = :id");
    $checkEstudiante->bindParam(':id', $estudiante, PDO::PARAM_INT);
    $checkEstudiante->execute();
    if ($checkEstudiante->fetchColumn() == 0) {
        $errores[] = "El estudiante seleccionado no existe.";
    }

    // Manejo del archivo (si se subió)
    $archivo_incidente = null;
    if (isset($_FILES['archivo_incidente']) && $_FILES['archivo_incidente']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($_FILES['archivo_incidente']['type'], $allowed_types)) {
            $errores[] = "Tipo de archivo no permitido.";
        } elseif ($_FILES['archivo_incidente']['size'] > 5 * 1024 * 1024) {
            $errores[] = "El archivo es demasiado grande (máx. 5MB).";
        } else {
            $nombreArchivo = time() . '_' . basename($_FILES['archivo_incidente']['name']);
            $rutaDestino = '../reportes/' . $nombreArchivo;
            if (move_uploaded_file($_FILES['archivo_incidente']['tmp_name'], $rutaDestino)) {
                $archivo_incidente = $nombreArchivo;
            } else {
                $errores[] = "Error al subir el archivo.";
            }
        }
    }

    // Si no hay errores en la validación se guarda el incidente en la base de datos
    if (empty($errores)) {
        try {
            // Iniciar transacción
            $conexion->beginTransaction();

            // Insertar incidente
            $sql = $conexion->prepare("INSERT INTO incidentes(estudiante_id, usuario_reporta_id, fecha_incidente, hora_incidente, lugar, tipo, descripcion, archivo_incidente) VALUES (:estudiante_id, :usuario_reporta_id, :fecha_incidente, :hora_incidente, :lugar, :tipo, :descripcion, :archivo_incidente)");
            $sql->bindParam(':estudiante_id', $estudiante, PDO::PARAM_INT);
            $sql->bindParam(':usuario_reporta_id', $usuario_reporta, PDO::PARAM_INT);
            $sql->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $sql->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $sql->bindParam(':lugar', $lugar, PDO::PARAM_STR);
            $sql->bindParam(':fecha_incidente', $fecha_incidente, PDO::PARAM_STR);
            $sql->bindParam(':hora_incidente', $hora_incidente, PDO::PARAM_STR);
            $sql->bindParam(':archivo_incidente', $archivo_incidente, PDO::PARAM_STR);

            $sql->execute();
            $incidente_id = $conexion->lastInsertId(); // ID del incidente insertado (MySQL)

            // Obtener nombre y apellido del docente que reportó el incidente
            $sql_doc = $conexion->prepare("SELECT nombre FROM usuarios WHERE id = :id");
            $sql_doc->bindParam(':id', $usuario_reporta);
            $sql_doc->execute();
            $docenteData = $sql_doc->fetch(PDO::FETCH_OBJ);

            // Crear mensaje con el nombre del docente
            $mensaje = "Nuevo incidente registrado por el docente " . $docenteData->nombre;

            // Insertar notificación
            $sql_notif = $conexion->prepare("
                INSERT INTO notificaciones (mensaje, incidente_id, leida, fecha)
                VALUES (:mensaje, :incidente_id, FALSE, NOW())
            ");
            $sql_notif->bindParam(':mensaje', $mensaje);
            $sql_notif->bindParam(':incidente_id', $incidente_id);
            $sql_notif->execute();

            // Confirmar la transacción
            $conexion->commit();

            $_SESSION['exito'] = "Incidente registrado correctamente";
            header("Location: ../incidentesReportados.php");
            exit();
        } catch (PDOException $e) {
            $conexion->rollBack();
            echo "Error al registrar el incidente: " . $e->getMessage();
        }
    } else {
        $_SESSION['errores'] = $errores;
        header("Location: ../registroIncidentes.php");
        exit();
    }
} else {
    $_SESSION['errores'] = ["Error al registrar el incidente."];
    header("Location: ../registroIncidentes.php");
    exit();
}
