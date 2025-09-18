<?php
include("autorizacion/auth.php"); // valida login y arranca sesión

// Verificar que tenga rol de docente
if ($_SESSION['rol'] !== 'docente') {
    header("Location: index.php"); // si no lo mandamos al login
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
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="welcome-content">
                <h2>¡Bienvenido al Sistema de Gestión de Incidentes!</h2>
                <p>Estamos encantados de tenerte aquí. Como docente, puedes reportar incidentes, hacer seguimiento de los casos y mantener un entorno educativo seguro y organizado.</p>
                
                <div class="welcome-features">
                    <div class="feature">
                        <i class="fas fa-plus-circle"></i>
                        <span>Registrar nuevos incidentes</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-list"></i>
                        <span>Ver tus incidentes reportados</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-chart-line"></i>
                        <span>Seguimiento del estado de casos</span>
                    </div>
                </div>
                
                <div class="welcome-actions">
                    <a href="registroIncidentes.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Reportar Nuevo Incidente
                    </a>
                    <a href="incidentesReportados.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Ver Mis Incidentes
                    </a>
                </div>
            </div>
        </div>
        
        <div class="quick-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Reportar Incidente</h3>
                    <p>Comunica problemas técnicos, de infraestructura o situaciones que afecten el ambiente educativo.</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Seguimiento en Tiempo Real</h3>
                    <p>Monitorea el estado de tus reportes y recibe actualizaciones sobre su resolución.</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <div class="stat-info">
                    <h3>Soporte Continuo</h3>
                    <p>El equipo técnico está disponible para resolver tus incidentes de manera eficiente.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .welcome-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
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