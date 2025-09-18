<?php
include_once '../conexion/bd.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_incidente'], $_POST['tipo'], $_POST['descripcion'], $_POST['lugar'])) {
    $id_incidente = $_POST['id_incidente'];
    $tipo = trim($_POST['tipo']);
    $descripcion = trim($_POST['descripcion']);
    $lugar = trim($_POST['lugar']);
    $archivo_incidente = null;

    // 1. Obtener el archivo viejo de la BD
    $sqlOld = $conexion->prepare("SELECT archivo_incidente FROM incidentes WHERE id = :id_incidente");
    $sqlOld->bindParam(':id_incidente', $id_incidente, PDO::PARAM_INT);
    $sqlOld->execute();
    $incidenteOld = $sqlOld->fetch(PDO::FETCH_ASSOC);
    $archivoViejo = $incidenteOld ? $incidenteOld['archivo_incidente'] : null;

    // 2. Verificar si subieron un nuevo archivo
    if (isset($_FILES['archivo_incidente']) && $_FILES['archivo_incidente']['error'] === UPLOAD_ERR_OK) {
        // Generar nombre único
        $nombreArchivo = time() . '_' . basename($_FILES['archivo_incidente']['name']);
        $rutaDestino = '../reportes/' . $nombreArchivo;

        if (move_uploaded_file($_FILES['archivo_incidente']['tmp_name'], $rutaDestino)) {
            $archivo_incidente = $nombreArchivo;

            // 3. Eliminar archivo viejo si existía
            if ($archivoViejo && file_exists('../reportes/' . $archivoViejo)) {
                unlink('../reportes/' . $archivoViejo);
            }
        }
    } else {
        // Si no subieron archivo nuevo, mantenemos el viejo
        $archivo_incidente = $archivoViejo;
    }

    // 4. Actualizar en la BD
    $sql = $conexion->prepare("UPDATE incidentes SET tipo = :tipo, descripcion = :descripcion, lugar = :lugar, archivo_incidente = :archivo_incidente WHERE id = :id_incidente");
    $sql->bindParam(':id_incidente', $id_incidente, PDO::PARAM_INT);
    $sql->bindParam(':tipo', $tipo);
    $sql->bindParam(':descripcion', $descripcion);
    $sql->bindParam(':lugar', $lugar);
    $sql->bindParam(':archivo_incidente', $archivo_incidente);

    if ($sql->execute()) {
        header("Location: ../incidentesReportados.php");
        exit();
    } else {
        echo "Error al editar el incidente";
    }
} else {
    echo "Error en la solicitud";
}
?>
