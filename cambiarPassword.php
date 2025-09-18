<?php
include("autorizacion/auth.php"); // valida login y arranca sesión

// Verificar que tenga rol de admin,docente o tutor
if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'docente' && $_SESSION['rol'] !== 'tutor') {
    header("Location: index.php"); // si no lo mandamos al login
    exit();
}
include("templates/header.php");
?>

<?php include("templates/topbar.php"); ?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <h1 class="page-title"><i class="fas fa-lock"></i> Cambiar Contraseña</h1>

    <!-- Formulario para cambiar contraseña -->
    <div class="form-container">
        <div class="card">
            <div class="card-header">
                <h3>Actualizar Contraseña</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="controladores/cambiarPassword.php">
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
                    
                    <?php if (isset($_SESSION['exito'])) : ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?= $_SESSION['exito']; ?>
                        </div>
                        <?php unset($_SESSION['exito']); ?>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="password_actual">Contraseña Actual *</label>
                        <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                    </div>

                    <div class="form-group">
                        <label for="nueva_password">Nueva Contraseña *</label>
                        <input type="password" class="form-control"  id="nueva_password" name="nueva_password" required>
                        <small class="form-text">La contraseña debe tener al menos 5 caracteres.</small>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para el sistema de notificaciones */
    .notification-container {
        position: relative;
        margin-right: 15px;
    }

    .notification-bell {
        position: relative;
        background: none;
        border: none;
        font-size: 20px;
        color: #555;
        cursor: pointer;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .notification-bell:hover {
        background: #f5f7fb;
        color: #4361ee;
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

    .notification-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #dc3545;
        color: white;
        font-size: 10px;
        font-weight: bold;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .notification-dropdown {
        position: absolute;
        top: 60px;
        right: 0;
        width: 380px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        display: none;
        overflow: hidden;
        animation: fadeIn 0.3s ease;
    }

    .notification-dropdown.active {
        display: block;
    }

    .notification-header {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f3f4;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notification-header h3 {
        font-size: 16px;
        color: #1e1e2c;
    }

    .notification-header a {
        color: #4361ee;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    .notification-list {
        max-height: 350px;
        overflow-y: auto;
    }

    .notification-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f9f9f9;
        display: flex;
        gap: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .notification-item:hover {
        background: #fafbff;
    }

    .notification-item.unread {
        background: #f0f7ff;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4361ee;
        flex-shrink: 0;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-size: 14px;
        font-weight: 600;
        color: #1e1e2c;
        margin-bottom: 5px;
    }

    .notification-desc {
        font-size: 13px;
        color: #666;
        line-height: 1.4;
        margin-bottom: 5px;
    }

    .notification-time {
        font-size: 11px;
        color: #888;
    }

    .notification-footer {
        padding: 15px 20px;
        text-align: center;
        border-top: 1px solid #f1f3f4;
    }

    .notification-footer a {
        color: #4361ee;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Estilos para el formulario */
    .form-container {
        max-width: 600px;
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
        color: #1e1e2c;
        font-size: 20px;
    }

    .card-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
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
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .form-text {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f1f3f4;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        box-shadow: 0 6px 18px rgba(67, 97, 238, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
    }

    .btn-secondary {
        background: #f8f9fa;
        color: #555;
        border: 2px solid #e6e6e6;
    }

    .btn-secondary:hover {
        background: #e9ecef;
    }

    .alert {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert-success {
        background: #e8f5e8;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    .alert-error {
        background: #ffebee;
        color: #dc3545;
        border: 1px solid #f5c6cb;
    }

    /* Ajustes responsivos */
    @media (max-width: 768px) {
        .notification-dropdown {
            width: 320px;
            right: -20px;
        }

        .form-container {
            padding: 0 15px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .notification-dropdown {
            width: 280px;
        }

        .card-body {
            padding: 20px;
        }
    }
</style>

<script>
    // Funcionalidad para el menú de notificaciones
    document.addEventListener('DOMContentLoaded', function() {
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');

        // Alternar menú de notificaciones
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
        });

        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });

        // Prevenir que el clic dentro del menú lo cierre
        notificationDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Validación de contraseña en tiempo real
        const nuevaPassword = document.getElementById('nueva_password');
        const confirmarPassword = document.getElementById('confirmar_password');

        function validarPassword() {
            if (nuevaPassword.value !== confirmarPassword.value) {
                confirmarPassword.style.borderColor = '#dc3545';
            } else {
                confirmarPassword.style.borderColor = '#e6e6e6';
            }
        }

        nuevaPassword.addEventListener('input', validarPassword);
        confirmarPassword.addEventListener('input', validarPassword);
    });
</script>

<?php include 'templates/footer.php'; ?>