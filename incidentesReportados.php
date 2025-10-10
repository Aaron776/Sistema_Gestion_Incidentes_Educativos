<?php
include("autorizacion/auth.php"); // valida login y arranca sesión

// Verificar que tenga rol de docente
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'docente') {
    $_SESSION['errores'] = ["No tienes permisos para acceder a esta sección."];
    header("Location: index.php");
    exit();
}

include("templates/header.php");
include_once 'conexion/bd.php';

// Obtener la lista de mis incidentes reportados como docente
$sql = $conexion->prepare("SELECT incidentes.id AS id_incidente, incidentes.fecha_incidente AS fecha_incidente, incidentes.hora_incidente AS hora_incidente, incidentes.lugar AS lugar,incidentes.tipo AS tipo, incidentes.descripcion AS descripcion, incidentes.archivo_incidente AS evidencia, incidentes.estado AS estado, estudiantes.nombre AS nombre_estudiante, estudiantes.apellido AS apellido_estudiante FROM incidentes INNER JOIN estudiantes  ON incidentes.estudiante_id = estudiantes.id WHERE usuario_reporta_id=:id_usuario ORDER BY incidentes.fecha_registro DESC");
$sql->bindParam(':id_usuario', $_SESSION['id_usuario']);
$sql->execute();
$lista_incidentes = $sql->fetchAll(PDO::FETCH_OBJ);
?>

<title>Incidentes Registrados - Sistema de Gestión</title>

<?php include("templates/topbar.php"); ?>

<style>
    :root {
        --primary: #3a0ca3;
        --secondary: #4361ee;
        --accent: #7209b7;
        --success: #4cc9f0;
        --warning: #f72585;
        --dark: #1e1e2c;
        --light: #f8f9fa;
        --sidebar: #2d3047;
        --sidebar-hover: #3d405b;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f5f7fb;
        color: var(--dark);
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 260px;
        background: var(--sidebar);
        color: white;
        transition: all 0.3s ease;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        z-index: 1000;
    }

    .sidebar-header {
        padding: 20px;
        background: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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

    .sidebar-header h2 {
        font-size: 22px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .sidebar-header h2 i {
        margin-right: 10px;
        color: var(--success);
    }

    .sidebar-menu {
        padding: 10px 0;
    }

    .menu-label {
        padding: 10px 20px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.5);
        margin-top: 15px;
    }

    .menu-item {
        padding: 12px 20px;
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.3s;
        position: relative;
    }

    .menu-item:hover,
    .menu-item.active {
        background: var(--sidebar-hover);
        color: white;
    }

    .menu-item i {
        width: 25px;
        margin-right: 10px;
        font-size: 18px;
    }

    .menu-item .badge {
        position: absolute;
        right: 20px;
        background: var(--accent);
        color: white;
        border-radius: 20px;
        padding: 3px 8px;
        font-size: 12px;
    }

    /* Main Content */
    .main-content {
        flex: 1;
        margin-left: 260px;
        transition: margin-left 0.3s ease;
    }

    /* Top Navigation */
    .top-nav {
        background: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 900;
    }

    .search-box {
        display: flex;
        align-items: center;
        background: #f5f7fb;
        border-radius: 20px;
        padding: 8px 15px;
        width: 300px;
    }

    .search-box i {
        color: #aaa;
        margin-right: 10px;
    }

    .search-box input {
        border: none;
        background: transparent;
        width: 100%;
        outline: none;
    }

    .user-menu {
        display: flex;
        align-items: center;
    }

    .notification {
        position: relative;
        margin-right: 20px;
        color: #555;
        cursor: pointer;
    }

    .notification .badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--warning);
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-profile {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 10px;
    }

    .user-info {
        line-height: 1.3;
    }

    .user-name {
        font-weight: 600;
        font-size: 14px;
    }

    .user-role {
        font-size: 12px;
        color: #888;
    }

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

    /* Panel de Control de Incidentes */
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

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #3bb5d8;
    }

    /* Tabla de Incidentes */
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

    .btn-delete {
        background: rgba(247, 37, 133, 0.15);
        color: var(--warning);
    }

    .btn-delete:hover {
        background: var(--warning);
        color: white;
    }

    /* Paginación */
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

        .menu-toggle {
            display: block;
        }

        .control-panel {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .actions {
            width: 100%;
            justify-content: flex-end;
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

        .filters {
            width: 100%;
        }

        .filter-select {
            min-width: 100%;
        }

        .incidents-table {
            min-width: 800px;
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

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 10px;
        width: 100%;
        max-width: 600px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .modal-title {
        font-size: 20px;
        font-weight: 600;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #999;
    }

    .modal-body {
        margin-bottom: 20px;
    }

    .modal-field {
        margin-bottom: 15px;
    }

    .modal-field label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
        color: #666;
        font-size: 14px;
    }

    .modal-field .value {
        font-size: 15px;
        color: #333;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
</style>
</head>

<body>
    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <h1 class="page-title"><i class="fas fa-list"></i> Incidentes Registrados</h1>

        <!-- Panel de Control -->
        <div class="control-panel">
            <div class="filters">
                <div class="filter-group">
                    <label for="filter-status">Estado</label>
                    <select id="filter-status" class="filter-select">
                        <option value="all">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="investigacion">Investigacion</option>
                        <option value="resuelto">Resuelto</option>
                    </select>
                </div>
            </div>

            <div class="actions">
                <a href="registroIncidentes.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nuevo Incidente
                </a>
            </div>
        </div>

        <!-- Tabla de Incidentes -->
        <div class="incidents-table-container">
            <?php if (isset($_SESSION['exito'])) : ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['exito']; ?>
                </div>
                <?php unset($_SESSION['exito']); ?>
            <?php endif; ?>
            <table class="incidents-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Estudiante</th>
                        <th>Fecha Incidente</th>
                        <th>Hora Incidente</th>
                        <th>Lugar</th>
                        <th>Tipo</th>
                        <th>Descripcion</th>
                        <th>Estado</th>
                        <th>Evidencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="incidents-table-body">
                    <?php foreach ($lista_incidentes as $item) { ?>
                        <tr data-estado="<?php echo strtolower($item->estado); ?>">
                            <td><?php echo htmlspecialchars($item->id_incidente); ?></td>
                            <td><?php echo htmlspecialchars($item->nombre_estudiante) . ' ' . htmlspecialchars($item->apellido_estudiante); ?></td>
                            <td><?php echo $item->fecha_incidente; ?></td>
                            <td><?php echo $item->hora_incidente; ?></td>
                            <td><?php echo $item->lugar; ?></td>
                            <td><?php echo $item->tipo; ?></td>
                            <td><?php echo $item->descripcion; ?></td>
                            <?php if ($item->estado == 'pendiente') { ?>
                                <td><span class="status-tag status-open"><?php echo htmlspecialchars($item->estado); ?></span></td>
                            <?php } elseif ($item->estado == 'investigacion') { ?>
                                <td><span class="status-tag status-progress"><?php echo htmlspecialchars($item->estado); ?></span></td>
                            <?php } else { ?>
                                <td><span class="status-tag status-resolved"><?php echo htmlspecialchars($item->estado); ?></span></td>
                            <?php } ?>
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
                                    <a href="editarIncidente.php?id_incidente=<?php echo $item->id_incidente; ?>" class="btn-icon btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
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
    </div>

    <!-- Script JS para filtrar tabla por estado -->
    <script>
        const selectEstado = document.getElementById('filter-status');
        const tablaCuerpo = document.getElementById('incidents-table-body');

        selectEstado.addEventListener('change', function() {
            const estadoSeleccionado = this.value.toLowerCase();
            const filas = tablaCuerpo.querySelectorAll('tr');

            filas.forEach(fila => {
                const estadoFila = fila.getAttribute('data-estado');
                if (estadoSeleccionado === 'all' || estadoFila === estadoSeleccionado) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    </script>
    <?php include 'templates/footer.php'; ?>