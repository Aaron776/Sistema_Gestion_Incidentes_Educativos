<?php
include("autorizacion/auth.php"); // valida login y arranca sesión

// Verificar que tenga rol de docente
if ($_SESSION['rol'] !== 'docente') {
    header("Location: index.php"); // lo mandamos al login
    exit();
}

include("templates/header.php");
include_once 'conexion/bd.php';

// Obtener incidente para editar
$id_incidente = $_GET['id_incidente'];
$sql = $conexion->prepare("SELECT estudiantes.nombre AS nombre_estudiante, estudiantes.apellido AS apellido_estudiante, incidentes.tipo AS tipo, incidentes.descripcion AS descripcion, incidentes.lugar AS lugar, incidentes.archivo_incidente AS evidencia FROM incidentes INNER JOIN estudiantes ON incidentes.estudiante_id = estudiantes.id WHERE incidentes.id=:id_incidente");
$sql->bindParam(':id_incidente', $id_incidente);
$sql->execute();
$incidente = $sql->fetch(PDO::FETCH_OBJ);
?>
<title>Editar Incidente - Sistema de Gestión</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Dashboard Content */
    .dashboard-content {
        padding: 30px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .page-title i {
        margin-right: 10px;
        color: var(--primary);
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

    /* Formulario de Registro de Incidentes */
    .form-container {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .form-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .form-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--dark);
        display: flex;
        align-items: center;
    }

    .form-title i {
        margin-right: 10px;
        color: var(--primary);
    }

    .form-description {
        color: #777;
        margin-top: 5px;
        font-size: 14px;
    }

    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }

    .form-group {
        flex: 1 0 calc(50% - 20px);
        margin: 0 10px 20px;
        position: relative;
        min-width: 250px;
    }

    .form-group.full-width {
        flex: 1 0 calc(100% - 20px);
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--dark);
        font-size: 14px;
    }

    .form-group label .required {
        color: var(--warning);
    }

    .input-with-icon {
        position: relative;
    }

    .input-with-icon i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
    }

    .input-with-icon input,
    .input-with-icon select,
    .input-with-icon textarea {
        width: 100%;
        padding: 12px 15px 12px 45px;
        border: 2px solid #e6e6e6;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .input-with-icon textarea {
        min-height: 120px;
        resize: vertical;
    }

    .input-with-icon input:focus,
    .input-with-icon select:focus,
    .input-with-icon textarea:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(58, 12, 163, 0.2);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .btn {
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--secondary);
    }

    .btn-secondary {
        background: #f5f7fb;
        color: #666;
    }

    .btn-secondary:hover {
        background: #e6e9ef;
    }

    .priority-tag {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 8px;
    }

    .priority-high {
        background: rgba(247, 37, 133, 0.15);
        color: var(--warning);
    }

    .priority-medium {
        background: rgba(114, 9, 183, 0.15);
        color: var(--accent);
    }

    .priority-low {
        background: rgba(76, 201, 240, 0.15);
        color: var(--success);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .sidebar {
            width: 80px;
            transform: translateX(-80px);
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .sidebar-header h2 span {
            display: none;
        }

        .menu-label,
        .menu-item span {
            display: none;
        }

        .menu-item i {
            margin-right: 0;
            font-size: 24px;
        }

        .submenu {
            display: none;
        }

        .menu-toggle {
            display: block;
        }
    }

    .menu-toggle {
        display: none;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 5px;
        width: 40px;
        height: 40px;
        font-size: 20px;
        cursor: pointer;
        margin-right: 15px;
    }

    @media (max-width: 768px) {
        .top-nav {
            padding: 15px;
        }

        .search-box {
            width: 200px;
        }

        .user-info {
            display: none;
        }

        .menu-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dashboard-content {
            padding: 15px;
        }

        .form-group {
            flex: 1 0 calc(100% - 20px);
        }
    }

    /* Toggle sidebar on mobile */
    @media (max-width: 992px) {
        .sidebar {
            transform: translateX(-260px);
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .overlay.open {
            display: block;
        }
    }
</style>

<?php include("templates/topbar.php"); ?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <h1 class="page-title"><i class="fas fa-plus-circle"></i> Editar Incidente</h1>

    <!-- Formulario de Registro de Incidentes -->
    <div class="form-container">
        <div class="form-header">
            <div class="form-title"><i class="fas fa-exclamation-triangle"></i> Información del Incidente</div>
            <div class="form-description">Complete todos los campos obligatorios <span class="required">*</span> para registrar un nuevo incidente con un estudiante.</div>
        </div>

        <form id="incidentForm" method="post" action="controladores/editarIncidente.php" enctype="multipart/form-data">
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
            <input type="hidden" name="id_incidente" value="<?php echo $id_incidente; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="incidentTitle">Tipo de Incidente <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-heading"></i>
                        <select id="incidentCategory" name="tipo" required>
                            <?php if ($incidente->tipo == 'disciplina') { ?>
                                <option value="disciplina" selected>Disciplina</option>
                                <option value="academico">Académico</option>
                                <option value="convivencia">Convivencia</option>
                                <option value="otro">Otro</option>
                            <?php } else if ($incidente->tipo == 'academico') { ?>
                                <option value="academico" selected>Académico</option>
                                <option value="disciplina">Disciplina</option>
                                <option value="convivencia">Convivencia</option>
                                <option value="otro">Otro</option>
                            <?php } else if ($incidente->tipo == 'convivencia') { ?>
                                <option value="convivencia" selected>Convivencia</option>
                                <option value="disciplina">Disciplina</option>
                                <option value="academico">Académico</option>
                                <option value="otro">Otro</option>
                            <?php } else { ?>
                                <option value="otro" selected>Otro</option>
                                <option value="disciplina">Disciplina</option>
                                <option value="academico">Académico</option>
                                <option value="convivencia">Convivencia</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="incidentPriority">Lugar <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" id="incidentPriority" name="lugar" placeholder="Ej: Laboratorio 1" value="<?php echo $incidente->lugar; ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="incidentTime">Evidencia <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-camera"></i>
                        <input type="file" id="incidentTime" name="archivo_incidente">
                    </div>
                </div>
            </div>

            <div class="form-group full-width">
                <label for="incidentDescription">Descripción Detallada <span class="required">*</span></label>
                <div class="input-with-icon">
                    <i class="fas fa-align-left"></i>
                    <textarea id="incidentDescription" name="descripcion" placeholder="Describa el incidente con todos los detalles relevantes..." required> <?php echo $incidente->descripcion; ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="btnCancel">Cancelar</button>
                <button type="submit" class="btn btn-primary">Editar Incidente</button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
    // Toggle sidebar on mobile
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('open');
        document.querySelector('.overlay').classList.toggle('open');
    });

    // Close sidebar when clicking overlay
    document.querySelector('.overlay').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.remove('open');
        document.querySelector('.overlay').classList.remove('open');
    });

    // Set current date and time as default
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const dateString = now.toISOString().split('T')[0];
        const timeString = now.toTimeString().substring(0, 5);

        document.getElementById('incidentDate').value = dateString;
        document.getElementById('incidentTime').value = timeString;
    });
</script>
<?php include 'templates/footer.php'; ?>