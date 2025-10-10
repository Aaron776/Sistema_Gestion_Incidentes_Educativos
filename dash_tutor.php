<?php
include("autorizacion/auth.php"); // valida login y arranca sesión

// Verificar que tenga rol de tutor
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'tutor') {
    http_response_code(403);
    echo json_encode([]);
    exit();
}

include("templates/header.php");
?>

<?php include("templates/topbar.php"); ?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <h1 class="page-title"><i class="fas fa-tachometer-alt"></i> Bienvenido, <?php echo $_SESSION['nombre']; ?></h1>
    
    <div class="welcome-container">
        <div class="welcome-card">
            <div class="welcome-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="welcome-content">
                <h2>¡Bienvenido al Sistema de Gestión de Incidentes!</h2>
                <p>Como tutor, tienes acceso a herramientas especializadas para el seguimiento y gestión de incidentes relacionados con tus estudiantes.</p>
                
                <div class="welcome-features">
                    <div class="feature">
                        <i class="fas fa-eye"></i>
                        <span>Monitorear incidentes de estudiantes</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-comments"></i>
                        <span>Comunicarte con el personal docente</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-chart-line"></i>
                        <span>Seguimiento del progreso académico</span>
                    </div>
                </div>
                
                <div class="welcome-actions">
                    <a href="incidentesMisEstudiantes.php" class="btn btn-primary">
                        <i class="fas fa-list"></i> Ver Incidentes de Estudiantes
                    </a>
                </div>
            </div>
        </div>
        
        <div class="quick-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Gestión de Estudiantes</h3>
                    <p>Accede a la información de tus estudiantes y su historial de incidentes.</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-info">
                    <h3>Reportes Detallados</h3>
                    <p>Genera reportes completos sobre el comportamiento y progreso de tus estudiantes.</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-info">
                    <h3>Colaboración</h3>
                    <p>Trabaja en conjunto con docentes y administración para resolver situaciones.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Acceso Rápido -->
    <div class="quick-access-section">
        <h2 class="section-title"><i class="fas fa-rocket"></i> Acceso Rápido</h2>
        
        <div class="quick-access-grid">
            <a href="incidentes-estudiantes.php" class="quick-access-card">
                <div class="quick-access-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Incidentes</h3>
                <p>Revisa los incidentes reportados de tus estudiantes</p>
            </a>
            
            <a href="estudiantes.php" class="quick-access-card">
                <div class="quick-access-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Mis Estudiantes</h3>
                <p>Gestiona la información de tus estudiantes asignados</p>
            </a>
            
            <a href="comunicaciones.php" class="quick-access-card">
                <div class="quick-access-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Comunicaciones</h3>
                <p>Contacta con docentes y administración</p>
            </a>
            
            <a href="reportes.php" class="quick-access-card">
                <div class="quick-access-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>Reportes</h3>
                <p>Genera reportes y estadísticas</p>
            </a>
        </div>
    </div>
</div>

<style>
    .welcome-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .welcome-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 30px;
    }
    
    .welcome-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 40px;
    }
    
    .welcome-content {
        flex: 1;
    }
    
    .welcome-content h2 {
        color: var(--dark);
        margin-bottom: 15px;
        font-size: 24px;
    }
    
    .welcome-content p {
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
        font-size: 16px;
    }
    
    .welcome-features {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 25px;
    }
    
    .feature {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #555;
    }
    
    .feature i {
        color: var(--primary);
        width: 20px;
    }
    
    .welcome-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .quick-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: flex-start;
        gap: 20px;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background: rgba(67, 97, 238, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 24px;
    }
    
    .stat-info h3 {
        color: var(--dark);
        margin-bottom: 10px;
        font-size: 18px;
    }
    
    .stat-info p {
        color: #666;
        line-height: 1.5;
        font-size: 14px;
    }
    
    .quick-access-section {
        margin-top: 30px;
    }
    
    .section-title {
        color: var(--dark);
        margin-bottom: 20px;
        font-size: 22px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .quick-access-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .quick-access-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .quick-access-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .quick-access-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        margin-bottom: 15px;
    }
    
    .quick-access-card h3 {
        color: var(--dark);
        margin-bottom: 10px;
        font-size: 18px;
    }
    
    .quick-access-card p {
        color: #666;
        line-height: 1.5;
        font-size: 14px;
    }
    
    .btn {
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--secondary);
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: #f5f7fb;
        color: #555;
    }
    
    .btn-secondary:hover {
        background: #e6e9ef;
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .welcome-card {
            flex-direction: column;
            text-align: center;
        }
        
        .welcome-actions {
            justify-content: center;
        }
        
        .feature {
            justify-content: center;
        }
        
        .quick-stats {
            grid-template-columns: 1fr;
        }
        
        .stat-card {
            flex-direction: column;
            text-align: center;
            align-items: center;
        }
        
        .quick-access-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Toggle sidebar on mobile
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('open');
        document.querySelector('.overlay').classList.toggle('open');
    });
</script>

<?php include("templates/footer.php"); ?>