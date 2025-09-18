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
        <div class="user-profile">
            <div class="user-avatar"><?php echo substr($_SESSION['nombre'], 0, 1); ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo $_SESSION['nombre']; ?></div>
                <div class="user-role"><?php echo ucfirst($_SESSION['rol']); ?> </div>
            </div>
        </div>
    </div>
</div>