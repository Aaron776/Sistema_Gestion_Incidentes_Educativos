<?php
session_start();
include_once '../conexion/bd.php';

// Verificar rol
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'docente')) {
    $_SESSION['errores'] = ["No tienes permisos para editar incidentes."];
    header("Location: ../editarIncidente.php?id_incidente=" . $id_incidente);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_incidente'], $_POST['tipo'], $_POST['descripcion'], $_POST['lugar'])) {
    $id_incidente = $_POST['id_incidente'];
    $tipo = trim($_POST['tipo']);
    $descripcion = trim($_POST['descripcion']);
    $lugar = trim($_POST['lugar']);
    $archivo_incidente = null;
    $errores=[];

    // ---------------- VALIDACIONES ----------------
    if (!filter_var($id_incidente, FILTER_VALIDATE_INT)) {
        $errores[] = "ID de incidente inválido.";
    }

    if (empty($tipo)) {
        $errores[] = "El tipo es obligatorio.";
    } elseif (strlen($tipo) > 100) {
        $errores[] = "El tipo no debe superar los 100 caracteres.";
    }

   
    if (empty($descripcion)) {
        $errores[] = "La descripción es obligatoria.";
    }elseif ($descripcion !== strip_tags($descripcion)) {
        $errores[] = 'No se permiten etiquetas HTML en la descripción';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $descripcion)) {
        $errores[] = 'La descripción contiene contenido no permitido';
    }


    if (empty($lugar)) {
        $errores[] = "El lugar es obligatorio.";
    }elseif ($lugar !== strip_tags($lugar)) {
        $errores[] = 'No se permiten etiquetas HTML en el lugar';
    } elseif (preg_match('/(viagra|casino|bitcoin|porno)/i', $lugar)) {
        $errores[] = 'El lugar contiene contenido no permitido';
    }elseif (strlen($lugar) > 150) {
        $errores[] = "El lugar no debe superar los 150 caracteres.";
    }

    // Verificar que el incidente exista
    if (empty($errores)) {
        $check = $conexion->prepare("SELECT archivo_incidente FROM incidentes WHERE id = :id_incidente");
        $check->bindParam(':id_incidente', $id_incidente, PDO::PARAM_INT);
        $check->execute();
        $incidenteOld = $check->fetch(PDO::FETCH_ASSOC);

        if (!$incidenteOld) {
            $errores[] = "El incidente no existe.";
        } else {
            $archivoViejo = $incidenteOld['archivo_incidente'] ?? null;
        }
    }


    // 1. Obtener el archivo viejo de la BD
    $sqlOld = $conexion->prepare("SELECT archivo_incidente FROM incidentes WHERE id = :id_incidente");
    $sqlOld->bindParam(':id_incidente', $id_incidente, PDO::PARAM_INT);
    $sqlOld->execute();
    $incidenteOld = $sqlOld->fetch(PDO::FETCH_ASSOC);
    $archivoViejo = $incidenteOld ? $incidenteOld['archivo_incidente'] : null;

    // Validación y subida del archivo
    if (empty($errores) && isset($_FILES['archivo_incidente']) && $_FILES['archivo_incidente']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['archivo_incidente']['name'], PATHINFO_EXTENSION));
        $permitidos = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];

        if (!in_array($ext, $permitidos)) {
            $errores[] = "Tipo de archivo no permitido. Solo PDF, JPG, PNG o DOCX.";
        } elseif ($_FILES['archivo_incidente']['size'] > 5 * 1024 * 1024) {
            $errores[] = "El archivo no puede superar los 5 MB.";
        } else {
            $nombreArchivo = time() . '_' . basename($_FILES['archivo_incidente']['name']);
            $rutaDestino = '../reportes/' . $nombreArchivo;

            if (move_uploaded_file($_FILES['archivo_incidente']['tmp_name'], $rutaDestino)) {
                $archivo_incidente = $nombreArchivo;

                // Eliminar archivo viejo
                if (!empty($archivoViejo) && file_exists('../reportes/' . $archivoViejo)) {
                    unlink('../reportes/' . $archivoViejo);
                }
            }
        }
    } else {
        $archivo_incidente = $archivoViejo ?? null;
    }
    

    // Actualizar registro
    if (empty($errores)) {
        $sql = $conexion->prepare("UPDATE incidentes 
            SET tipo = :tipo, descripcion = :descripcion, lugar = :lugar, archivo_incidente = :archivo_incidente 
            WHERE id = :id_incidente");
        $sql->bindParam(':id_incidente', $id_incidente, PDO::PARAM_INT);
        $sql->bindParam(':tipo', $tipo);
        $sql->bindParam(':descripcion', $descripcion);
        $sql->bindParam(':lugar', $lugar);
        $sql->bindParam(':archivo_incidente', $archivo_incidente);
        $sql->execute();

        $_SESSION['exito'] = "Incidente actualizado correctamente.";
        header("Location: ../incidentesReportados.php");
        exit();

    } else {
        $_SESSION['errores'] = $errores;
        header("Location: ../editarIncidente.php?id_incidente=" . $id_incidente);
        exit();
    }
} else {
    $_SESSION['errores'] = ["Error en la solicitud."];
    header("Location: ../incidentesReportados.php");
    exit();
}
?>
