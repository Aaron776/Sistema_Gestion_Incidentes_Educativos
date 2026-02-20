<?php
include("autorizacion/auth.php"); // valida login y arranca sesión

// Verificar que tenga rol de admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    echo json_encode([]);
    exit();
}

include("templates/header.php");
include("controladores/dashAdmin.php");
?>


<!-- Top Navigation -->
<div class="top-nav">
    <div style="display: flex; align-items: center;">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Buscar...">
        </div>
    </div>

    <div class="user-menu">
        <!-- Contenedor de notificaciones -->
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin') { ?>
            <div class="notification-container">
                <button class="notification-bell" id="notificationBell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationCount">0</span>
                </button>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h3>Notificaciones</h3>
                        <a href="#" id="marcarLeidas">Marcar todas como leídas</a>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <!-- Aquí se cargarán las notificaciones con JS -->
                    </div>
                </div>
            </div>
        <?php } ?>


        <div class="user-profile">
            <div class="user-avatar"><?php echo substr($_SESSION['nombre'], 0, 1); ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo $_SESSION['nombre']; ?></div>
                <div class="user-role"><?php echo ucfirst($_SESSION['rol']); ?> </div>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <h1 class="page-title"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $cantidadIncidentes->total_incidentes; ?></h3>
                <p>Incidentes Totales</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $cantidadResueltos->total_resueltos; ?></h3>
                <p>Resueltos</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $cantidadInvestigacion->total_investigacion; ?></h3>
                <p>En Investigación</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon accent">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $cantidadPendientes->total_pendientes; ?></h3>
                <p>Pendientes</p>
            </div>
        </div>
    </div>

    <!-- Charts and Lists -->
    <div class="content-grid">
        <div class="left-column">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Incidentes por Categoría</div>
                    <div class="card-actions">
                        <a href="#">Ver Reporte Completo</a>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="incidentChart"></canvas>
                </div>
            </div>
        </div>

        <div class="right-column">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Últimos Incidentes</div>
                    <div class="card-actions">
                        <a href="gestionIncidentes.php">Ver Todos</a>
                    </div>
                </div>

                <ul class="incident-list">
                    <?php foreach ($ultimosIncidentes as $item) { ?>
                        <li class="incident-item">
                            <div class="incident-icon priority-high">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="incident-info">
                                <div class="incident-title"><?php echo $item->descripcion; ?></div>
                                <div class="incident-desc"><?php echo $item->nombre_estudiante . " " . $item->apellido_estudiante; ?></div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
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

    /* Ajustes responsivos */
    @media (max-width: 768px) {
        .notification-dropdown {
            width: 320px;
            right: -20px;
        }
    }

    @media (max-width: 480px) {
        .notification-dropdown {
            width: 280px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationList = document.getElementById('notificationList');
        const notificationCount = document.getElementById('notificationCount');
        const marcarLeidasBtn = document.getElementById('marcarLeidas');

        let panelAbierto = false;

        // ── 1. Actualizar badge con conteo de no leídas ──────────────────
        async function actualizarConteo() {
            try {
                const res = await fetch('obtener_notificaciones.php?solo_no_leidas=1');
                const data = await res.json();
                const total = data.length;

                notificationCount.textContent = total;
                notificationCount.style.display = total > 0 ? 'flex' : 'none';
            } catch (e) {
                console.error('Error al obtener conteo:', e);
            }
        }

        // ── 2. Cargar notificaciones en el panel ─────────────────────────
        async function cargarNotificaciones() {
            try {
                const res = await fetch('obtener_notificaciones.php');
                const data = await res.json();

                notificationList.innerHTML = '';

                if (data.length === 0) {
                    notificationList.innerHTML = `
                        <div class="notification-item" style="justify-content:center;color:#888;font-size:13px;">
                            <i class="fas fa-check-circle" style="margin-right:8px;color:#4caf50;"></i>
                            No hay notificaciones nuevas
                        </div>`;
                    return;
                }

                data.forEach(notif => {
                    const item = document.createElement('div');
                    item.classList.add('notification-item');
                    if (!notif.leida) item.classList.add('unread');

                    item.innerHTML = `
                        <div class="notification-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">Nuevo incidente reportado</div>
                            <div class="notification-desc">${notif.mensaje}</div>
                            <div class="notification-time"><i class="fas fa-clock" style="margin-right:4px;"></i>${notif.fecha}</div>
                        </div>
                    `;
                    notificationList.appendChild(item);
                });
            } catch (e) {
                console.error('Error al cargar notificaciones:', e);
            }
        }

        // ── 3. Marcar todas como leídas en BD y resetear badge ───────────
        async function marcarTodasLeidas() {
            try {
                await fetch('marcar_leidas.php', {
                    method: 'POST'
                });
                notificationCount.textContent = '0';
                notificationCount.style.display = 'none';
                // Quitar clase 'unread' de los items visibles
                document.querySelectorAll('.notification-item.unread')
                    .forEach(el => el.classList.remove('unread'));
            } catch (e) {
                console.error('Error al marcar leídas:', e);
            }
        }

        // ── 4. Toggle del panel ──────────────────────────────────────────
        notificationBell.addEventListener('click', async function(e) {
            e.stopPropagation();
            panelAbierto = !panelAbierto;
            notificationDropdown.classList.toggle('active', panelAbierto);

            if (panelAbierto) {
                await cargarNotificaciones();
                await marcarTodasLeidas();
            }
        });

        // ── 5. Botón "Marcar todas como leídas" ──────────────────────────
        marcarLeidasBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            await marcarTodasLeidas();
        });

        // ── 6. Cerrar al hacer clic fuera ────────────────────────────────
        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('active');
                panelAbierto = false;
            }
        });

        // ── 7. Polling inicial y periódico del badge ──────────────────────
        actualizarConteo();
        setInterval(actualizarConteo, 15000);
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById("incidentChart").getContext("2d");
        new Chart(ctx, {
            type: "doughnut", // Puedes cambiar a "bar", "pie", "line"
            data: {
                labels: ["Pendientes", "Resueltos", "En Investigación"],
                datasets: [{
                    label: "Cantidad de incidentes",
                    data: [
                        <?php echo $cantidadPendientes->total_pendientes; ?>,
                        <?php echo $cantidadResueltos->total_resueltos; ?>,
                        <?php echo $cantidadInvestigacion->total_investigacion; ?>
                    ],
                    backgroundColor: [
                        "#f44336", // rojo para pendientes
                        "#4caf50", // verde para resueltos
                        "#ff9800" // naranja para investigación
                    ],
                    borderColor: "#fff",
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: "Distribución de incidentes",
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
    });
</script>

<?php include 'templates/footer.php'; ?>