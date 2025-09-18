<?php
// Comprobar el estado actual de la sesión
if (session_status() === PHP_SESSION_NONE) { // Si no hay ninguna sesión activa
    session_start(); // Inicia una nueva sesión o reanuda la existente
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Incidentes Educativos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        .menu-item:hover, .menu-item.active {
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
        
        .submenu {
            padding-left: 30px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .submenu.open {
            max-height: 500px;
        }
        
        .submenu .menu-item {
            padding: 10px 15px;
            font-size: 14px;
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
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
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
        
        /* Charts and Tables */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .card-actions a {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .chart-container {
            height: 300px;
            position: relative;
        }
        
        .incident-list {
            list-style: none;
        }
        
        .incident-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .incident-item:last-child {
            border-bottom: none;
        }
        
        .incident-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 18px;
        }
        
        .incident-info {
            flex: 1;
        }
        
        .incident-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .incident-desc {
            font-size: 13px;
            color: #888;
        }
        
        .incident-time {
            font-size: 12px;
            color: #aaa;
        }
        
        .priority-high {
            background: var(--warning);
        }
        
        .priority-medium {
            background: var(--accent);
        }
        
        .priority-low {
            background: var(--success);
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
            
            .menu-label, .menu-item span {
                display: none;
            }
            
            .menu-item i {
                margin-right: 0;
                font-size: 24px;
            }
            
            .submenu {
                display: none;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
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
            
            .stats-grid {
                grid-template-columns: 1fr;
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
</head>
<body>
    <!-- Overlay for mobile -->
    <div class="overlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-exclamation-circle"></i> <span>Gestión de Incidentes</span></h2>
        </div>
        
        <?php if($_SESSION['rol'] == 'admin') { ?>
        <div class="sidebar-menu">
            <div class="menu-label">Principal</div>
            <a href="dash_admin.php" class="menu-item ">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard Admin</span>
            </a>
         <?php } else if($_SESSION['rol'] == 'docente'){ ?> 
            <div class="sidebar-menu">
            <div class="menu-label">Principal</div>
            <a href="dash_docente.php" class="menu-item ">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard Docente</span>
            </a> 
        <?php } else if($_SESSION['rol'] == 'tutor'){ ?> 
            <div class="sidebar-menu">
            <div class="menu-label">Principal</div>
            <a href="dash_tutor.php" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard Tutor</span>
            </a> 
        <?php } ?>
            
        <?php if($_SESSION['rol'] == 'admin') { ?>
            <div class="menu-label">Gestionar</div>
                <a href="gestionUsuarios.php" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Gestion de Usuarios</span>
                </a>

                <a href="gestionEstudiantes.php" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Gestion de Estudiantes</span>
                </a>

                <a href="gestionIncidentes.php" class="menu-item">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Incidentes</span>
                </a>
            <?php } ?>

            
            <?php if($_SESSION['rol'] == 'docente') { ?>
            <div class="menu-label">Docente</div>
                <a href="registroIncidentes.php" class="menu-item">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Registro de Incidentes</span>
                </a>

                <a href="incidentesReportados.php" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Mis Incidentes Reportados</span>
                </a>
            <?php } ?>

            
            <?php if($_SESSION['rol'] == 'tutor') { ?>
            <div class="menu-label">Tutor</div>
                <a href="incidentesMisEstudiantes.php" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Ver Incidentes de mis Estudiantes</span>
                </a>
            <?php } ?>
        
            <div class="menu-label">Administración</div>
            
            <a href="cambiarPassword.php" class="menu-item">
                <i class="fas fa-cog"></i>
                <span>Cambiar Contraseña</span>
            </a>
            
            <a href="controladores/logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        
   