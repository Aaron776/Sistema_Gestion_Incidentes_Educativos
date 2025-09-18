<?php
include("autorizacion/auth.php");

if ($_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include("templates/header.php");
include("conexion/bd.php");

// Obtener el ID del incidente desde la URL
$id_incidente = isset($_GET['id_incidente']) ? $_GET['id_incidente'] : '';


// Obtener información del incidente
$sql = $conexion->prepare("SELECT incidentes.*, estudiantes.nombre AS nombre_estudiante, usuarios.nombre AS usuario_reporta 
                          FROM incidentes 
                          INNER JOIN estudiantes ON incidentes.estudiante_id = estudiantes.id 
                          INNER JOIN usuarios ON incidentes.usuario_reporta_id = usuarios.id 
                          WHERE incidentes.id = :id_incidente");
$sql->bindParam(':id_incidente', $id_incidente, PDO::PARAM_INT);
$sql->execute();
$incidente = $sql->fetch(PDO::FETCH_OBJ);
?>

<?php include("templates/topbar.php"); ?>

<div class="dashboard-content">
    <h1 class="page-title"><i class="fas fa-edit"></i> Editar Estado del Incidente</h1>

    <div class="form-container">
        <div class="card">
            <div class="card-header">
                <h3>Incidente #INC-<?php echo $incidente->id; ?></h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="controladores/editarEstadoIncidente.php">
                    <?php if (isset($_SESSION['errores'])) : ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($_SESSION['errores'] as $error) : ?>
                                    <li><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['errores']); ?>
                    <?php endif; ?>
                    <input type="hidden" name="id_incidente" value="<?php echo $incidente->id; ?>">
                    <div class="form-group">
                        <label>Descripción del Incidente</label>
                        <div class="form-control-static"><?php echo $incidente->descripcion; ?></div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Estudiante</label>
                            <div class="form-control-static"><?php echo $incidente->nombre_estudiante; ?></div>
                        </div>

                        <div class="form-group">
                            <label>Reportado por</label>
                            <div class="form-control-static"><?php echo $incidente->usuario_reporta; ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Fecha del Incidente</label>
                            <div class="form-control-static"><?php echo $incidente->fecha_incidente; ?></div>
                        </div>

                        <div class="form-group">
                            <label>Hora del Incidente</label>
                            <div class="form-control-static"><?php echo $incidente->hora_incidente; ?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Lugar</label>
                        <div class="form-control-static"><?php echo $incidente->lugar; ?></div>
                    </div>

                    <div class="form-group">
                        <label>Tipo</label>
                        <div class="form-control-static"><?php echo $incidente->tipo; ?></div>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado Actual *</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="investigacion" <?php echo $incidente->estado == 'investigacion' ? 'selected' : ''; ?>>Investigación</option>
                            <option value="pendiente" <?php echo $incidente->estado == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="resuelto" <?php echo $incidente->estado == 'resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='gestionIncidentes.php'">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .breadcrumb {
        margin-left: 15px;
        font-size: 14px;
        color: #666;
    }

    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb span {
        color: #666;
    }

    .alert {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 3px;
            font-size: 14px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

    .form-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .card-header {
        padding: 20px 25px;
        border-bottom: 1px solid #f1f3f4;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .card-header h3 {
        margin: 0;
        color: var(--dark);
        font-size: 20px;
    }

    .card-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .form-control-static {
        padding: 12px 0;
        border-bottom: 1px solid #f1f3f4;
        color: #555;
        min-height: 20px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e6e6e6;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f1f3f4;
    }

    .alert {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert-danger {
        background: #ffebee;
        color: #dc3545;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 0;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<?php include("templates/footer.php"); ?>