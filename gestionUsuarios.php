<?php
include("autorizacion/auth.php");

// Validar que esté logueado y sea admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['errores'] = ["No tienes permisos para acceder a esta sección."];
    header("Location: index.php");
    exit();
}

include("templates/header.php");
include("conexion/bd.php");

// Obtener la lista de usuarios
$sql = $conexion->prepare("SELECT * FROM usuarios");
$sql->execute();
$usuarios = $sql->fetchAll(PDO::FETCH_OBJ);
?>

<?php include("templates/topbar.php"); ?>

<div class="dashboard-content">
    <h1 class="page-title"><i class="fas fa-users-cog"></i> Gestión de Usuarios</h1>

    <div class="crud-header">
        <div class="header-actions">
            <a href="registroUsuarios.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </a>
        </div>
        <select class="filter-select" id="roleFilter">
            <option value="">Todos los roles</option>
            <option value="admin">Administrador</option>
            <option value="docente">Docente</option>
            <option value="tutor">Tutor</option>
        </select>
    </div>

    <div class="table-container">
        <?php if (isset($_SESSION['exito'])) : ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['exito']; ?>
            </div>
            <?php unset($_SESSION['exito']); ?>
        <?php endif; ?>
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
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $item) : ?>
                    <tr>
                        <td>USR-<?php echo $item->id; ?></td>
                        <td><?php echo $item->nombre; ?></td>
                        <td><?php echo $item->email; ?></td>
                        <td><span class="badge badge-primary"><?php echo $item->rol; ?></span></td>
                        <td>
                            <div class="action-buttons">
                                <a href="editarUsuario.php?id_usuario=<?php echo $item->id; ?>" class="btn-icon btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="controladores/eliminarUsuario.php" class="formEliminar" method="POST">
                                    <input type="hidden" name="id_usuario" value="<?php echo $item->id; ?>">
                                    <button type="submit" class="btn-icon btn-delete">
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
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .header-filters {
        display: flex;
        gap: 10px;
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

    /* Estilos mejorados para el botón Nuevo Usuario */
    .btn.btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn.btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(67, 97, 238, 0.4);
    }

    .btn.btn-primary:active {
        transform: translateY(0);
        box-shadow: 0 3px 10px rgba(67, 97, 238, 0.3);
    }

    .header-filters {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    /* Estilos mejorados para los filtros */
    .filter-select {
        padding: 10px 16px;
        border: 2px solid #e6e6e6;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        background: white;
        color: #333;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 160px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23666'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        appearance: none;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .filter-select:hover {
        border-color: #ccc;
    }

    /* Etiqueta para los filtros */
    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: #666;
        margin-bottom: 5px;
        display: block;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    /* Responsive para filtros */
    @media (max-width: 768px) {
        .crud-header {
            flex-direction: column;
            align-items: stretch;
            padding: 15px;
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

        .btn.btn-primary {
            width: 100%;
            justify-content: center;
        }
    }


    .table-container {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .crud-table {
        width: 100%;
        border-collapse: collapse;
    }

    .crud-table th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #e9ecef;
    }

    .crud-table td {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .crud-table tr:hover {
        background: #f8f9fa;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-primary {
        background: var(--primary);
        color: white;
    }

    .badge-success {
        background: var(--success);
        color: white;
    }

    .badge-warning {
        background: var(--warning);
        color: white;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-edit {
        background: #e3f2fd;
        color: #1976d2;
    }

    .btn-edit:hover {
        background: #1976d2;
        color: white;
    }

    .btn-delete {
        background: #ffebee;
        color: #d32f2f;
    }

    .btn-delete:hover {
        background: #d32f2f;
        color: white;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 20px;
    }

    .pagination-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 6px;
        cursor: pointer;
    }

    .pagination-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .modal-content {
        background: white;
        border-radius: 10px;
        width: 90%;
        max-width: 600px;
        margin: 50px auto;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .modal-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group label {
        font-weight: 600;
        color: #333;
    }

    .form-group input,
    .form-group select {
        padding: 10px;
        border: 2px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary);
    }



    @media (max-width: 768px) {
        .crud-header {
            flex-direction: column;
            align-items: stretch;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .modal-content {
            width: 95%;
            margin: 20px auto;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Filtros
    document.getElementById('roleFilter').addEventListener('change', function() {
        const roleFilter = this.value.toLowerCase();
        const rows = document.querySelectorAll('.crud-table tbody tr');

        rows.forEach(row => {
            const role = row.querySelector('td:nth-child(4) span').textContent.toLowerCase();
            // Elimina espacios extra
            row.style.display = !roleFilter || role.trim() === roleFilter ? '' : 'none';
        });
    });
</script>

<script>
    document.querySelectorAll('.formEliminar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Está seguro que desea eliminar este usuario?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // enviar el formulario si confirma
                }
            });
        });
    });
</script>

<?php include("templates/footer.php"); ?>