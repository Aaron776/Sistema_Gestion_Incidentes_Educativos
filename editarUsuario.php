<?php
include("autorizacion/auth.php");

if ($_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}
include("templates/header.php");
include("conexion/bd.php");

// Obtener el ID del usuario a editar
$id_usuario = $_GET['id_usuario'];

// Obtener la información del usuario
$sql = $conexion->prepare("SELECT nombre,email,rol FROM usuarios WHERE id = :id_usuario");
$sql->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$sql->execute();
$usuario = $sql->fetch(PDO::FETCH_OBJ);
?>

<?php include("templates/topbar.php"); ?>

<div class="dashboard-content">
    <div class="page-header">
        <div class="header-back">
            <a href="gestionUsuarios.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Volver a Usuarios
            </a>
        </div>
        <h1 class="page-title"><i class="fas fa-user-edit"></i> Editar Usuario</h1>
        <p class="page-subtitle">Complete el formulario para editar la información de un usuario</p>
    </div>

    <div class="form-container">
        <form id="userForm" class="user-form" action="controladores/editarUsuarios.php" method="POST">
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
        
            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
            <!-- Información Personal -->
            <div class="form-section">
                <div class="section-header">
                    <i class="fas fa-user-circle"></i>
                    <h3>Información Personal</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre">Nombres *</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo $usuario->nombre; ?>" required placeholder="Ingrese los nombres">
                        <span class="form-help">Ej: María José</span>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico *</label>
                        <input type="email" id="email" name="email" value="<?php echo $usuario->email; ?>" required placeholder="usuario@instituto.edu">
                        <span class="form-help">Debe ser un correo valido</span>
                    </div>
                </div>
            </div>

            <!-- Rol y Permisos -->
            <div class="form-section">
                <div class="section-header">
                    <i class="fas fa-user-tag"></i>
                    <h3>Rol</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="rol">Rol del Usuario *</label>
                        <select id="rol" name="rol" required>
                            <option value="">Seleccione un rol</option>
                            <?php if ($usuario->rol === 'admin') { ?>
                                <option selected value="admin">Administrador</option>
                                <option value="docente">Docente</option>
                                <option value="tutor">Tutor</option>
                            <?php } elseif ($usuario->rol === 'docente') { ?>
                                <option value="admin">Administrador</option>
                                <option selected value="docente">Docente</option>
                                <option value="tutor">Tutor</option>
                            <?php } elseif ($usuario->rol === 'tutor') { ?>
                                <option value="admin">Administrador</option>
                                <option value="docente">Docente</option>
                                <option selected value="tutor">Tutor</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                    <i class="fas fa-redo"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .header-back {
        margin-bottom: 15px;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.3s ease;
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

    .back-button:hover {
        background: #f8f9fa;
        transform: translateX(-5px);
    }

    .page-subtitle {
        color: #666;
        margin-top: 8px;
        font-size: 16px;
    }

    .form-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .form-section {
        margin-bottom: 30px;
        padding-bottom: 25px;
        border-bottom: 1px solid #eee;
    }

    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .section-header i {
        color: var(--primary);
        font-size: 20px;
    }

    .section-header h3 {
        color: var(--dark);
        margin: 0;
        font-size: 18px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
    .form-group select {
        padding: 12px 16px;
        border: 2px solid #e6e6e6;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        transform: translateY(-1px);
    }

    .form-help {
        font-size: 12px;
        color: #888;
        font-style: italic;
    }

    .password-input {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 4px;
    }

    .toggle-password:hover {
        color: var(--primary);
    }

    .password-strength {
        margin-top: 8px;
    }

    .strength-bar {
        height: 4px;
        background: #e6e6e6;
        border-radius: 2px;
        margin-bottom: 4px;
        overflow: hidden;
    }

    .strength-bar::after {
        content: '';
        display: block;
        height: 100%;
        width: 0%;
        background: #dc3545;
        transition: all 0.3s ease;
    }

    .strength-text {
        font-size: 11px;
        color: #888;
    }

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }

    .permission-category {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid var(--primary);
    }

    .permission-category h4 {
        margin: 0 0 15px 0;
        color: var(--dark);
        font-size: 16px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        cursor: pointer;
        font-weight: normal;
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
    }

    .checkbox-label input[type="checkbox"]:checked+.checkmark {
        background: var(--primary);
        border-color: var(--primary);
    }

    .checkbox-label input[type="checkbox"]:checked+.checkmark::after {
        content: '✓';
        color: white;
        font-size: 12px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid #eee;
    }

    .btn {
        padding: 12px 24px;
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

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .permissions-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .page-header {
            padding: 20px;
        }

        .form-container {
            padding: 20px;
        }
    }

    /* Validación visual */
    .form-group input:invalid:not(:placeholder-shown) {
        border-color: #dc3545;
    }

    .form-group input:valid:not(:placeholder-shown) {
        border-color: #28a745;
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-section {
        animation: fadeIn 0.5s ease forwards;
    }

    .form-section:nth-child(1) {
        animation-delay: 0.1s;
    }

    .form-section:nth-child(2) {
        animation-delay: 0.2s;
    }

    .form-section:nth-child(3) {
        animation-delay: 0.3s;
    }

    .form-section:nth-child(4) {
        animation-delay: 0.4s;
    }
</style>

<script>
    // Función para mostrar/ocultar contraseña
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        const icon = button.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Validación de fortaleza de contraseña
    document.getElementById('password').addEventListener('input', function(e) {
        const password = e.target.value;
        const strengthBar = document.querySelector('.strength-bar');
        const strengthText = document.querySelector('.strength-text');

        let strength = 0;
        let color = '#dc3545';
        let text = 'Débil';

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;

        if (strength === 2) {
            color = '#ffc107';
            text = 'Moderada';
        } else if (strength === 3) {
            color = '#28a745';
            text = 'Fuerte';
        } else if (strength >= 4) {
            color = '#20c997';
            text = 'Muy fuerte';
        }

        strengthBar.style.setProperty('--strength-color', color);
        strengthBar.querySelector('::after').style.width = (strength * 25) + '%';
        strengthText.textContent = text;
        strengthText.style.color = color;
    });





    function resetForm() {
        if (confirm('¿Está seguro de que desea limpiar el formulario?')) {
            document.getElementById('userForm').reset();
        }
    }

    // Carga inicial
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar la barra de fortaleza
        const style = document.createElement('style');
        style.textContent = `
        .strength-bar::after {
            background: var(--strength-color, #dc3545);
            width: var(--strength-width, 0%);
        }
    `;
        document.head.appendChild(style);
    });
</script>
<?php include("templates/footer.php"); ?>