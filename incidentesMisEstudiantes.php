<?php
include("autorizacion/auth.php"); // valida login y arranca sesión

// Verificar que tenga rol de tutor
if ($_SESSION['rol'] !== 'tutor') {
    header("Location: index.php");
    exit();
}

include("templates/header.php");
include("conexion/bd.php");

// Obtener la lista de incidentes de mis estudiantes que estan a cargo mio como tutor
$sql = $conexion->prepare("SELECT incidentes.fecha_incidente AS fecha_incidente, incidentes.hora_incidente AS hora_incidente,incidentes.id AS id_incidente, incidentes.lugar AS lugar,incidentes.tipo AS tipo, incidentes.descripcion AS descripcion, incidentes.archivo_incidente AS evidencia, incidentes.estado AS estado, estudiantes.nombre AS nombre_estudiante, estudiantes.apellido AS apellido_estudiante FROM incidentes INNER JOIN estudiantes ON incidentes.estudiante_id = estudiantes.id  WHERE estudiantes.tutor_id=:id_tutor ORDER BY incidentes.fecha_incidente DESC");
$sql->execute(array(':id_tutor' => $_SESSION['id_usuario']));
$listaEstudiantes = $sql->fetchAll(PDO::FETCH_OBJ);


// Obtener cantidad de estudiantes que tengo como tutor o bajo mi tutoria
$sql = $conexion->prepare("SELECT count(*) AS total_estudiantes FROM estudiantes where tutor_id=:id_tutor");
$sql->execute(array(':id_tutor' => $_SESSION['id_usuario']));
$cantidadEstudiantes = $sql->fetch(PDO::FETCH_OBJ);

// Obtener cantidad de incidentes en estado de investigacion
$sql = $conexion->prepare("SELECT count(*) AS total_incidentes FROM incidentes INNER JOIN estudiantes ON incidentes.estudiante_id = estudiantes.id where estudiantes.tutor_id=:id_tutor and incidentes.estado='investigacion'");
$sql->execute(array(':id_tutor' => $_SESSION['id_usuario']));
$sql->execute();
$cantidadIncidentesInvestigacion = $sql->fetch(PDO::FETCH_OBJ);

// Obtener cantidad de incidentes en estado de resuelto
$sql = $conexion->prepare("SELECT count(*) AS total_incidentes FROM incidentes INNER JOIN estudiantes ON incidentes.estudiante_id = estudiantes.id where estudiantes.tutor_id=:id_tutor and incidentes.estado='resuelto'");
$sql->execute(array(':id_tutor' => $_SESSION['id_usuario']));
$sql->execute();
$cantidadIncidentesResueltos= $sql->fetch(PDO::FETCH_OBJ);
?>

<?php include("templates/topbar.php"); ?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <h1 class="page-title"><i class="fas fa-exclamation-triangle"></i> Incidentes de Mis Estudiantes</h1>

    <!-- Panel de Control -->
    <div class="control-panel">
        <div class="filters">
            <div class="filter-group">
                <label for="filter-student">Estudiante</label>
                <select id="filter-student" class="filter-select">
                    <option value="all">Todos los estudiantes</option>
                    <option value="1">María González</option>
                    <option value="2">Carlos Rodríguez</option>
                    <option value="3">Ana Martínez</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-status">Estado</label>
                <select id="filter-status" class="filter-select">
                    <option value="all">Todos los estados</option>
                    <option value="open">Abierto</option>
                    <option value="progress">En progreso</option>
                    <option value="resolved">Resuelto</option>
                </select>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-primary">
                <i class="fas fa-download"></i> Exportar
            </button>
            <button class="btn btn-secondary">
                <i class="fas fa-filter"></i> Aplicar Filtros
            </button>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $cantidadEstudiantes->total_estudiantes; ?></h3>
                <p>Estudiantes a cargo</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $cantidadIncidentesInvestigacion->total_incidentes; ?></h3>
                <p>Incidentes en Investigación</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $cantidadIncidentesResueltos->total_incidentes; ?></h3>
                <p>Incidentes resueltos</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon accent">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>65%</h3>
                <p>Tasa de resolución</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Incidentes -->
    <div class="incidents-table-container">
        <table class="incidents-table">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Fecha Incidente</th>
                    <th>Hora del Incidente</th>
                    <th>Lugar</th>
                    <th>Tipo</th>
                    <th>Descripcion</th>
                    <th>Estado</th>
                    <th>Evidencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaEstudiantes as $item) : ?>
                    <tr>
                        <td>
                            <div class="student-info">
                                <div class="student-avatar"><?php echo substr($item->nombre_estudiante, 0, 1); ?></div>
                                <div class="student-details">
                                    <div class="student-name"><?php echo $item->nombre_estudiante; ?> <?php echo $item->apellido_estudiante; ?></div>
                                    <div class="student-id">ID: <?php echo $item->id_incidente; ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $item->fecha_incidente; ?></td>
                        <td><?php echo $item->hora_incidente; ?></td>
                        <td><?php echo $item->lugar; ?></td>
                        <td><?php echo $item->tipo; ?></td>
                        <td><?php echo $item->descripcion; ?></td>
                        <td>
                            <?php if ($item->estado == 'investigacion') : ?>
                                <span class="status-tag status-progress">Investigación</span>
                            <?php elseif ($item->estado == 'resuelto') : ?>
                                <span class="status-tag status-resolved">Resuelto</span>
                            <?php elseif ($item->estado == 'pendiente') : ?>
                                <span class="status-tag status-open">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($item->evidencia)) {
                                $rutaArchivo = "reportes/" . $item->evidencia;
                                $extension = strtolower(pathinfo($item->evidencia, PATHINFO_EXTENSION));
                                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) { ?>
                                    <a href="<?php echo $rutaArchivo; ?>" target="_blank">
                                        <img src="<?php echo $rutaArchivo; ?>" alt="Evidencia" style="width:60px; border-radius:5px;">
                                    </a>
                                <?php } else { ?>
                                    <a href="<?php echo $rutaArchivo; ?>" target="_blank">Ver archivo</a>
                                <?php }
                            } else { ?>
                                <span>Sin evidencia</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="seguimientoIncidente.php?id_incidente=<?php echo $item->id_incidente; ?>&&id_tutor=<?php echo $_SESSION['id_usuario']; ?>" class="btn-icon btn-comment">
                                    <i class="fas fa-comment"></i> 
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="pagination">
            <button class="pagination-button">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="pagination-button active">1</button>
            <button class="pagination-button">2</button>
            <button class="pagination-button">3</button>
            <button class="pagination-button">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<style>
    .student-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }

    .student-details {
        line-height: 1.3;
    }

    .student-name {
        font-weight: 600;
        font-size: 14px;
    }

    .student-id {
        font-size: 12px;
        color: #888;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    /* Botón de comentarios */
    .btn-comment {
        background: rgba(76, 201, 240, 0.15);
        color: var(--success);
    }

    .btn-comment:hover {
        background: var(--success);
        color: white;
    }

    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 15px;
        color: white;
    }

    .stat-icon.primary {
        background: var(--primary);
    }

    .stat-icon.success {
        background: var(--success);
    }

    .stat-icon.warning {
        background: var(--warning);
    }

    .stat-icon.accent {
        background: var(--accent);
    }

    .stat-info h3 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-info p {
        color: #888;
        font-size: 14px;
    }

    .control-panel {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
    }

    .filters {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #666;
    }

    .filter-select {
        padding: 8px 12px;
        border: 2px solid #e6e6e6;
        border-radius: 6px;
        font-size: 14px;
        min-width: 150px;
    }

    .actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 10px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
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
        color: #555;
    }

    .btn-secondary:hover {
        background: #e6e9ef;
    }

    .incidents-table-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        overflow-x: auto;
    }

    .incidents-table {
        width: 100%;
        border-collapse: collapse;
    }

    .incidents-table th {
        text-align: left;
        padding: 15px;
        font-weight: 600;
        color: #666;
        border-bottom: 2px solid #f0f0f0;
    }

    .incidents-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .incidents-table tr:hover {
        background: #f9f9f9;
    }

    .priority-tag {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
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

    .status-tag {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-open {
        background: rgba(247, 37, 133, 0.15);
        color: var(--warning);
    }

    .status-progress {
        background: rgba(67, 97, 238, 0.15);
        color: var(--primary);
    }

    .status-resolved {
        background: rgba(76, 201, 240, 0.15);
        color: var(--success);
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        font-size: 14px;
    }

    .btn-view {
        background: rgba(67, 97, 238, 0.15);
        color: var(--primary);
    }

    .btn-view:hover {
        background: var(--primary);
        color: white;
    }

    .btn-edit {
        background: rgba(76, 201, 240, 0.15);
        color: var(--success);
    }

    .btn-edit:hover {
        background: var(--success);
        color: white;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        gap: 8px;
    }

    .pagination-button {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 1px solid #e6e6e6;
        cursor: pointer;
        transition: all 0.3s;
    }

    .pagination-button:hover,
    .pagination-button.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    @media (max-width: 768px) {
        .control-panel {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .actions {
            width: 100%;
            justify-content: flex-end;
        }

        .filters {
            width: 100%;
        }

        .filter-select {
            min-width: 100%;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .incidents-table {
            min-width: 800px;
        }
    }
</style>

<script>
    // Toggle sidebar on mobile
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('open');
        document.querySelector('.overlay').classList.toggle('open');
    });

    // Filtros de búsqueda
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('.incidents-table tbody tr');

        rows.forEach(row => {
            const studentName = row.querySelector('.student-name').textContent.toLowerCase();
            const incidentText = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const typeText = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

            if (studentName.includes(searchText) || incidentText.includes(searchText) || typeText.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Filtros de selección
    document.getElementById('filter-student').addEventListener('change', filterTable);
    document.getElementById('filter-status').addEventListener('change', filterTable);
    document.getElementById('filter-severity').addEventListener('change', filterTable);

    function filterTable() {
        const studentFilter = document.getElementById('filter-student').value;
        const statusFilter = document.getElementById('filter-status').value;
        const severityFilter = document.getElementById('filter-severity').value;

        const rows = document.querySelectorAll('.incidents-table tbody tr');

        rows.forEach(row => {
            const studentId = row.querySelector('.student-id').textContent.split(': ')[1];
            const status = row.querySelector('td:nth-child(7) span').textContent.toLowerCase();
            const severity = row.querySelector('td:nth-child(5) span').textContent.toLowerCase();

            const studentMatch = studentFilter === 'all' || studentId === studentFilter;
            const statusMatch = statusFilter === 'all' || status === statusFilter;
            const severityMatch = severityFilter === 'all' || severity === severityFilter;

            if (studentMatch && statusMatch && severityMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<?php include("templates/footer.php"); ?>