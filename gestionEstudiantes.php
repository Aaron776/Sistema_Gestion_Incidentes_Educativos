<?php
include("autorizacion/auth.php");
if ($_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}
include("templates/header.php");
include("conexion/bd.php");

// Obtener la lista de estudiantes
$sql = $conexion->prepare("SELECT estudiantes.id, estudiantes.nombre, estudiantes.apellido,estudiantes.curso,estudiantes.email,estudiantes.telefono,usuarios.nombre AS tutor  FROM estudiantes  LEFT JOIN  usuarios ON estudiantes.tutor_id = usuarios.id ORDER BY estudiantes.id DESC");
$sql->execute();
$estudiantes = $sql->fetchAll(PDO::FETCH_OBJ);
?>

<?php include("templates/topbar.php"); ?>

<div class="dashboard-content">
    <h1 class="page-title"><i class="fas fa-user-graduate"></i> Gestión de Estudiantes</h1>

    <div class="crud-header">
        <div class="header-actions">
            <a href="registroEstudiantes.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Estudiante
            </a>
            <a class="btn btn-secondary">
                <i class="fas fa-download"></i> Exportar
            </a>
        </div>
        <div class="header-filters">
            <div class="filter-group">
                <label class="filter-label">Grado</label>
                <select class="filter-select" id="gradoFilter">
                    <option value="">Todos los grados</option>
                    <option value="7">7° Grado</option>
                    <option value="8">8° Grado</option>
                    <option value="9">9° Grado</option>
                    <option value="10">10° Grado</option>
                    <option value="11">11° Grado</option>
                </select>
            </div>
        </div>
    </div>

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
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Grado/Curso</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Tutor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $item) : ?>
                    <tr>
                        <td><span class="student-id">EST-<?php echo $item->id; ?></span></td>
                        <td>
                            <div class="student-info">
                                <div class="student-avatar"><?php echo substr($item->nombre, 0, 2); ?></div>
                                <div class="student-details">
                                    <div class="student-name"><?php echo $item->nombre; ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $item->apellido; ?></td>
                        <td><span class="grade-badge"><?php echo $item->curso; ?>°</span></td>
                        <td><?php echo $item->email; ?></td>
                        <td><?php echo $item->telefono; ?></td>
                        <td>
                            <?php if ($item->tutor != null) { ?>
                                <?php echo $item->tutor; ?>
                            <?php } else { ?>
                                Sin Tutor
                            <?php } ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="editarEstudiante.php?id_estudiante=<?php echo $item->id; ?>" class="btn-icon btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="controladores/eliminarEstudiantes.php" method="POST">
                                        <input type="hidden" name="id_estudiante" value="<?php echo $item->id; ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <button class="pagination-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="pagination-btn active">1</button>
        <button class="pagination-btn">2</button>
        <button class="pagination-btn">3</button>
        <button class="pagination-btn"><i class="fas fa-chevron-right"></i></button>
    </div>
</div>

<style>
    /* Estilos generales mejorados */
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .header-actions {
        display: flex;
        gap: 15px;
    }

    .header-filters {
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: #666;
    }

    .filter-select {
        padding: 10px 12px;
        border: 2px solid #e6e6e6;
        border-radius: 8px;
        font-size: 14px;
        min-width: 140px;
        background: white;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    /* Botones mejorados */
    .btn {
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(67, 97, 238, 0.4);
    }

    .btn-secondary {
        background: #f8f9fa;
        color: #666;
        border: 2px solid #e6e6e6;
    }

    .btn-secondary:hover {
        background: #e9ecef;
        transform: translateY(-1px);
    }

    /* Estadísticas */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
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

    .stat-icon.info {
        background: var(--accent);
    }

    .stat-info h3 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        color: var(--dark);
    }

    .stat-info p {
        color: #666;
        margin: 0;
        font-size: 14px;
    }

    /* Tabla mejorada */
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
    }

    .crud-table {
        width: 100%;
        border-collapse: collapse;
    }

    .crud-table th {
        background: #f8f9fa;
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #e9ecef;
        font-size: 14px;
    }

    .crud-table td {
        padding: 16px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .crud-table tr:hover {
        background: #f8f9fa;
    }

    /* Información del estudiante */
    .student-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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
        color: var(--dark);
        font-size: 14px;
    }

    .student-email {
        font-size: 12px;
        color: #666;
    }

    .student-id {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #666;
    }

    /* Badges */
    .grade-badge {
        padding: 6px 12px;
        border-radius: 15px;
        background: #e3f2fd;
        color: #1976d2;
        font-weight: 600;
        font-size: 12px;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.inactive {
        background: #f8d7da;
        color: #721c24;
    }

    /* Botones de acción */
    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-view {
        background: #e8f5e8;
        color: #2e7d32;
    }

    .btn-view:hover {
        background: #2e7d32;
        color: white;
        transform: scale(1.1);
    }

    .btn-edit {
        background: #e3f2fd;
        color: #1976d2;
    }

    .btn-edit:hover {
        background: #1976d2;
        color: white;
        transform: scale(1.1);
    }

    .btn-delete {
        background: #ffebee;
        color: #d32f2f;
    }

    .btn-delete:hover {
        background: #d32f2f;
        color: white;
        transform: scale(1.1);
    }

    /* Paginación */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .pagination-btn {
        width: 40px;
        height: 40px;
        border: 2px solid #e6e6e6;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .pagination-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .pagination-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Modal mejorado */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        margin: 50px auto;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s ease;
    }

    .modal-header {
        padding: 25px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        border-radius: 12px 12px 0 0;
    }

    .modal-title {
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
    }

    .modal-close:hover {
        color: var(--primary);
    }

    .modal-body {
        padding: 25px;
    }

    .modal-footer {
        padding: 20px 25px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        background: #f8f9fa;
        border-radius: 0 0 12px 12px;
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

    /* Formulario modal */
    .modal-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 12px 16px;
        border: 2px solid #e6e6e6;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
        font-family: inherit;
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
            transform: translateY(-50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .crud-header {
            flex-direction: column;
            align-items: stretch;
        }

        .header-actions {
            order: 2;
            justify-content: center;
        }

        .header-filters {
            order: 1;
            flex-direction: column;
            gap: 10px;
        }

        .filter-select {
            min-width: 100%;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .modal-content {
            width: 95%;
            margin: 20px auto;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    function openModal(action, id = null) {
        const modal = document.getElementById('studentModal');
        const title = document.getElementById('modalTitle');

        if (action === 'create') {
            title.innerHTML = '<i class="fas fa-user-plus"></i> Nuevo Estudiante';
            document.getElementById('studentForm').reset();
        } else {
            title.innerHTML = '<i class="fas fa-edit"></i> Editar Estudiante';
            // Cargar datos del estudiante
        }

        modal.style.display = 'block';
    }

    function closeModal() {
        document.getElementById('studentModal').style.display = 'none';
    }

    function saveStudent() {
        const form = document.getElementById('studentForm');
        if (form.checkValidity()) {
            // Simular guardado
            alert('Estudiante guardado correctamente');
            closeModal();
        } else {
            form.reportValidity();
        }
    }

    function viewStudent(id) {
        alert('Vista detallada del estudiante ' + id);
    }

    function confirmDelete(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este estudiante?')) {
            alert('Estudiante eliminado correctamente');
        }
    }

    // Búsqueda en tiempo real
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.crud-table tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Filtros
    document.getElementById('gradoFilter').addEventListener('change', filterTable);
    document.getElementById('grupoFilter').addEventListener('change', filterTable);
    document.getElementById('estadoFilter').addEventListener('change', filterTable);

    function filterTable() {
        const gradoFilter = document.getElementById('gradoFilter').value;
        const grupoFilter = document.getElementById('grupoFilter').value;
        const estadoFilter = document.getElementById('estadoFilter').value;
        const rows = document.querySelectorAll('.crud-table tbody tr');

        rows.forEach(row => {
            const grado = row.querySelector('td:nth-child(3)').textContent;
            const grupo = row.querySelector('td:nth-child(3)').textContent;
            const estado = row.querySelector('td:nth-child(7) span').textContent.toLowerCase();

            const gradoMatch = !gradoFilter || grado.includes(gradoFilter);
            const grupoMatch = !grupoFilter || grupo.includes(grupoFilter);
            const estadoMatch = !estadoFilter || estado.includes(estadoFilter);

            row.style.display = gradoMatch && grupoMatch && estadoMatch ? '' : 'none';
        });
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
        const modal = document.getElementById('studentModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

<?php include("templates/footer.php"); ?>