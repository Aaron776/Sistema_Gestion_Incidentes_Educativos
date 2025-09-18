<?php
include("autorizacion/auth.php");

if ($_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include("templates/header.php");
include("conexion/bd.php");

// Obtener la lista de incidentes
$sql = $conexion->prepare("SELECT  usuarios.id AS id_usuario_reporta,usuarios.nombre AS usuario_reporta , incidentes.id as id_incidente,incidentes.descripcion AS descripcion, estudiantes.nombre AS nombre_estudiante,incidentes.fecha_incidente AS fecha_incidente, incidentes.hora_incidente AS hora_incidente, incidentes.lugar AS lugar, incidentes.archivo_incidente,incidentes.estado AS estado, incidentes.tipo AS tipo FROM incidentes INNER JOIN estudiantes ON incidentes.estudiante_id = estudiantes.id INNER JOIN usuarios ON incidentes.usuario_reporta_id = usuarios.id ORDER BY incidentes.id DESC");
$sql->execute();
$listaIncidentes = $sql->fetchAll(PDO::FETCH_OBJ);
?>

<?php include("templates/topbar.php"); ?>

<div class="dashboard-content">
    <h1 class="page-title"><i class="fas fa-exclamation-triangle"></i> Gestión de Incidentes - Administrador</h1>

    <!-- Panel de Control -->
    <div class="control-panel">
        <div class="header-actions">
            <a href="reportesPdf/reportesIncidentesPdf.php" target="_blank" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="reportesExcel/reportesIncidentesExcel.php" target="_blank" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
        <div class="header-filters">
            <div class="filter-group">
                <label class="filter-label">Estado</label>
                <select class="filter-select" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="resuelto">Resuelto</option>
                    <option value="cerrado">Cerrado</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Categoría</label>
                <select class="filter-select" id="categoryFilter">
                    <option value="">Todas las categorías</option>
                    <option value="tecnico">Técnico</option>
                    <option value="academico">Académico</option>
                    <option value="conductual">Conductual</option>
                    <option value="infraestructura">Infraestructura</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de Incidentes -->
    <div class="table-container">
        <?php if (isset($_SESSION['exito'])) : ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['exito']; ?>
            </div>
            <?php unset($_SESSION['exito']); ?>
        <?php endif; ?>
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripcion</th>
                    <th>Tipo</th>
                    <th>Estudiante</th>
                    <th>Reportado por</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaIncidentes as $item) { ?>
                    <tr>
                        <td><span class="incident-id">INC-<?php echo $item->id_incidente; ?></span></td>
                        <td>
                            <div class="incident-info">
                                <div class="incident-title"><?php echo $item->descripcion; ?></div>
                            </div>
                        </td>
                        <td><span class="badge badge-technical"><?php echo $item->tipo; ?></span></td>
                        <td>
                            <div class="reporter-info">
                                <div class="reporter-name"><?php echo $item->nombre_estudiante; ?></div>
                            </div>
                        </td>
                        <td><?php echo $item->usuario_reporta; ?><br></td>
                        <td><span class="incident-date"><?php echo $item->fecha_incidente; ?></span></td>
                        <td>
                            <?php echo $item->estado; ?><br>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="editarEstadoIncidente.php?id_incidente=<?php echo $item->id_incidente; ?>" class="btn-icon btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="controladores/eliminarIncidente.php" method="POST">
                                    <button type="submit" class="btn-icon btn-delete" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <input type="hidden" name="id_incidente" value="<?php echo $item->id_incidente; ?>">
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="pagination">
        <button class="pagination-btn" onclick="changePage('prev')">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="pagination-btn active">1</button>
        <button class="pagination-btn">2</button>
        <button class="pagination-btn">3</button>
        <span class="pagination-ellipsis">...</span>
        <button class="pagination-btn">10</button>
        <button class="pagination-btn" onclick="changePage('next')">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>
<style>
    /* Estilos generales mejorados */
    :root {
        --primary: #4361ee;
        --secondary: #3a0ca3;
        --success: #4cc9f0;
        --warning: #f72585;
        --danger: #dc3545;
        --dark: #1e1e2c;
        --light: #f8f9fa;
    }

    .dashboard-content {
        padding: 25px;
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

    .page-title {
        color: var(--dark);
        margin-bottom: 25px;
        font-size: 28px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Panel de Control Mejorado */
    .control-panel {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 20px;
        padding: 25px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-left: 5px solid var(--primary);
    }

    .header-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .header-filters {
        display: flex;
        gap: 20px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-select {
        padding: 12px 16px;
        border: 2px solid #e6e6e6;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        background: white;
        transition: all 0.3s ease;
        min-width: 160px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23666'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 16px;
        appearance: none;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        transform: translateY(-1px);
    }

    /* Botones Mejorados */
    .btn {
        padding: 14px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        display: flex;
        align-items: center;
        gap: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        box-shadow: 0 6px 18px rgba(67, 97, 238, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        box-shadow: 0 6px 18px rgba(40, 167, 69, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    }

    .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        color: white;
        box-shadow: 0 6px 18px rgba(23, 162, 184, 0.3);
    }

    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(23, 162, 184, 0.4);
    }

    /* Estadísticas Mejoradas */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .stat-card:nth-child(1) {
        border-left-color: var(--primary);
    }

    .stat-card:nth-child(2) {
        border-left-color: var(--warning);
    }

    .stat-card:nth-child(3) {
        border-left-color: var(--success);
    }

    .stat-card:nth-child(4) {
        border-left-color: var(--danger);
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.primary {
        background: var(--primary);
    }

    .stat-icon.warning {
        background: var(--warning);
    }

    .stat-icon.success {
        background: var(--success);
    }

    .stat-icon.danger {
        background: var(--danger);
    }

    .stat-info h3 {
        font-size: 32px;
        font-weight: 800;
        margin: 0;
        color: var(--dark);
        line-height: 1;
    }

    .stat-info p {
        color: #666;
        margin: 8px 0 4px 0;
        font-size: 14px;
        font-weight: 600;
    }

    .stat-trend {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 12px;
        display: inline-block;
    }

    .stat-card:nth-child(1) .stat-trend {
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary);
    }

    .stat-card:nth-child(2) .stat-trend {
        background: rgba(247, 37, 133, 0.1);
        color: var(--warning);
    }

    .stat-card:nth-child(3) .stat-trend {
        background: rgba(76, 201, 240, 0.1);
        color: var(--success);
    }

    .stat-card:nth-child(4) .stat-trend {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger);
    }

    /* Tabla Mejorada */
    .table-container {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .crud-table {
        width: 100%;
        border-collapse: collapse;
    }

    .crud-table th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 18px;
        text-align: left;
        font-weight: 700;
        color: var(--dark);
        border-bottom: 3px solid #e9ecef;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .crud-table td {
        padding: 18px;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }

    .crud-table tr {
        transition: all 0.3s ease;
    }

    .crud-table tr:hover {
        background: #fafbff;
        transform: scale(1.01);
    }

    /* Información del Incidente */
    .incident-info {
        line-height: 1.4;
    }

    .incident-title {
        font-weight: 600;
        color: var(--dark);
        font-size: 15px;
        margin-bottom: 4px;
    }

    .incident-desc {
        font-size: 13px;
        color: #666;
        line-height: 1.3;
    }

    .incident-id {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        color: var(--primary);
        font-size: 13px;
    }

    /* Información del Reportero */
    .reporter-info {
        line-height: 1.3;
    }

    .reporter-name {
        font-weight: 600;
        color: var(--dark);
        font-size: 14px;
    }

    .reporter-role {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
    }

    .time {
        font-size: 11px;
        color: #888;
        font-weight: 500;
    }

    /* Badges Mejorados */
    .badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-technical {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        color: #1976d2;
        border: 1px solid #bbdefb;
    }

    .badge-academic {
        background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
        color: #7b1fa2;
        border: 1px solid #e1bee7;
    }

    .badge-infrastructure {
        background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    /* Badges de Prioridad */
    .priority-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .priority-badge.critical {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
        color: #d32f2f;
        border: 1px solid #ffcdd2;
    }

    .priority-badge.high {
        background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
        color: #f57c00;
        border: 1px solid #ffe0b2;
    }

    .priority-badge.medium {
        background: linear-gradient(135deg, #fff9c4 0%, #fff59d 100%);
        color: #fbc02d;
        border: 1px solid #fff59d;
    }

    .priority-badge.low {
        background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
        color: #388e3c;
        border: 1px solid #c8e6c9;
    }

    /* Select de Estado Mejorado */
    .status-select {
        padding: 10px 14px;
        border: 2px solid #e6e6e6;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 140px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23666'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 14px;
        appearance: none;
    }

    .status-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    /* Botones de Acción Mejorados */
    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 15px;
        position: relative;
    }

    .btn-icon::after {
        content: attr(title);
        position: absolute;
        bottom: -30px;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 11px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .btn-icon:hover::after {
        opacity: 1;
        visibility: visible;
        bottom: -25px;
    }

    .btn-view {
        background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
        color: #2e7d32;
    }

    .btn-view:hover {
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
        color: white;
        transform: scale(1.1);
    }

    .btn-edit {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        color: #1976d2;
    }

    .btn-edit:hover {
        background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%);
        color: white;
        transform: scale(1.1);
    }

    .btn-comment {
        background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
        color: #f57c00;
    }

    .btn-comment:hover {
        background: linear-gradient(135deg, #f57c00 0%, #e65100 100%);
        color: white;
        transform: scale(1.1);
    }

    .btn-history {
        background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
        color: #7b1fa2;
    }

    .btn-history:hover {
        background: linear-gradient(135deg, #7b1fa2 0%, #4a148c 100%);
        color: white;
        transform: scale(1.1);
    }

    /* Paginación Mejorada */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 30px;
    }

    .pagination-btn {
        width: 45px;
        height: 45px;
        border: 2px solid #e6e6e6;
        background: white;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .pagination-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        transform: translateY(-2px);
    }

    .pagination-btn.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border-color: var(--primary);
        transform: scale(1.05);
    }

    .pagination-ellipsis {
        padding: 0 10px;
        color: #666;
        font-weight: 600;
    }

    /* Modal Mejorado */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(5px);
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        width: 90%;
        max-width: 500px;
        margin: 50px auto;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .modal-header {
        padding: 25px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px 20px 0 0;
    }

    .modal-header h3 {
        color: var(--dark);
        margin: 0;
        font-size: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
        transition: color 0.3s ease;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        background: #ffebee;
        color: #dc3545;
    }

    .modal-body {
        padding: 30px;
    }

    .modal-footer {
        padding: 25px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        background: #f8f9fa;
        border-radius: 0 0 20px 20px;
    }

    /* Formulario Modal */
    .modal-form {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .form-group label {
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .form-control {
        padding: 14px 16px;
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
        transform: translateY(-1px);
    }

    .date-range {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .date-range input {
        flex: 1;
    }

    .date-separator {
        color: #666;
        font-weight: 600;
        padding: 0 5px;
    }

    .checkbox-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        font-weight: normal;
        padding: 8px;
        border-radius: 8px;
        transition: background 0.3s ease;
    }

    .checkbox-label:hover {
        background: #f8f9fa;
    }

    .checkbox-label input[type="checkbox"] {
        display: none;
    }

    .checkmark {
        width: 20px;
        height: 20px;
        border: 2px solid #ddd;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .checkbox-label input[type="checkbox"]:checked+.checkmark {
        background: var(--primary);
        border-color: var(--primary);
    }

    .checkbox-label input[type="checkbox"]:checked+.checkmark::after {
        content: '✓';
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-60px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .control-panel {
            flex-direction: column;
            align-items: stretch;
            gap: 25px;
        }

        .header-actions {
            justify-content: center;
        }

        .header-filters {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .dashboard-content {
            padding: 15px;
        }

        .header-filters {
            flex-direction: column;
            gap: 15px;
        }

        .filter-select {
            min-width: 100%;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .crud-table {
            display: block;
            overflow-x: auto;
        }

        .action-buttons {
            flex-direction: column;
        }

        .modal-content {
            width: 95%;
            margin: 20px auto;
        }

        .checkbox-group {
            grid-template-columns: 1fr;
        }

        .date-range {
            flex-direction: column;
            align-items: stretch;
        }

        .date-separator {
            text-align: center;
            padding: 10px 0;
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: 24px;
        }

        .stat-card {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .stat-icon {
            margin-bottom: 15px;
        }

        .pagination {
            flex-wrap: wrap;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Efectos de carga */
    .skeleton-loading {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Scroll personalizado */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Mejoras de accesibilidad */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* Modo oscuro opcional */
    @media (prefers-color-scheme: dark) {

        .table-container,
        .stat-card,
        .control-panel,
        .modal-content {
            background: #2d3748;
            color: #e2e8f0;
        }

        .crud-table th {
            background: #4a5568;
            color: #e2e8f0;
        }

        .crud-table tr:hover {
            background: #4a5568;
        }

        .form-control,
        .filter-select,
        .status-select {
            background: #4a5568;
            border-color: #718096;
            color: #e2e8f0;
        }
    }
</style>
<?php include("templates/footer.php"); ?>