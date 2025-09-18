<?php
include("autorizacion/auth.php"); // valida login y arranca sesión

// Verificar que tenga rol de tutor
if ($_SESSION['rol'] !== 'tutor') {
    header("Location: index.php");
    exit();
}

include("templates/header.php");
include("conexion/bd.php");

$id_incidente = $_GET['id_incidente']; // ID del incidente que se está seguimiento
$id_tutor = $_SESSION['id_usuario']; // ID del tutor que está seguimiento el incidente


// Obtener cantidad de comentarios
$sql = $conexion->prepare("SELECT COUNT(*) AS total_comentarios FROM comentarios_incidente WHERE incidente_id=:id_incidente");
$sql->bindParam(':id_incidente', $id_incidente);
$sql->execute();
$cantidadComentarios = $sql->fetch(PDO::FETCH_OBJ);

// Obtener la lista de comentarios de este incidente de mi estudiante
$sql=$conexion->prepare("SELECT * FROM comentarios_incidente WHERE incidente_id=:id_incidente ORDER BY fecha DESC LIMIT 10");
$sql->bindParam(':id_incidente', $id_incidente);
$sql->execute();
$comentarios = $sql->fetchAll(PDO::FETCH_OBJ);

?>

<?php include("templates/topbar.php"); ?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <h1 class="page-title"><i class="fas fa-comments"></i> Comentarios - Incidente #<?php echo $id_incidente; ?></h1>

    <!-- Formulario para Agregar Comentario -->
    <div class="comment-form-card">
        <h3><i class="fas fa-plus-circle"></i> Agregar Nuevo Comentario</h3>
        <form id="commentForm" class="comment-form" method="post" action="controladores/seguimientoIncidente.php">
        <input type="hidden" name="incidente_id" value="<?php echo $id_incidente; ?>">
        <input type="hidden" name="tutor_id" value="<?php echo $id_tutor; ?>">
            <div class="form-group">
                <label for="commentText">Comentario <span class="required">*</span></label>
                <textarea id="commentText" name="comentario" class="form-control" rows="4" placeholder="Escribe tu comentario aquí..." required></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Publicar Comentario
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Comentarios -->
    <div class="comments-list-section">
        <div class="section-header">
            <h3><i class="fas fa-list"></i> Comentarios Existentes</h3>
            <span class="comments-count"><?php echo $cantidadComentarios->total_comentarios; ?> comentarios</span>
        </div>
        
        <div class="comments-list">
            <?php foreach ($comentarios as $item) { ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <div class="comment-author-info">
                            <div class="author-avatar"><?php echo substr($_SESSION['nombre'], 0, 1); ?></div>
                            <div class="author-details">
                                <div class="author-name"><?php echo $_SESSION['nombre']; ?> (Tú)</div>
                            </div>
                        </div>
                        <div class="comment-meta">
                            <span class="comment-date"><?php echo  date('H:i d/m/Y ', strtotime($item->fecha)); ?></span>
                            <div class="comment-actions">
                                <a href="editarComentarioSeguimiento.php?id_incidente=<?php echo $id_incidente; ?>&id_comentario=<?php echo $item->id; ?>" class="btn-icon btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="post" action="controladores/eliminarComentario.php?id_incidente=<?php echo $id_incidente; ?>">
                                    <input type="hidden" name="comentario_id" value="<?php echo $item->id; ?>">
                                    <button class="btn-icon btn-delete" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="comment-content">
                        <p><?php echo $item->comentario; ?></p>
                    </div>
                    <div class="comment-footer">
                        <span class="comment-status"><i class="fas fa-check-circle"></i> Publicado</span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<style>
    /* Tarjeta de información del incidente */
    .incident-info-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        border-left: 5px solid var(--primary);
    }
    
    .incident-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }
    
    .incident-header h3 {
        color: var(--dark);
        font-size: 20px;
        margin: 0;
        flex: 1;
    }
    
    .incident-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .detail-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .detail-label {
        font-weight: 600;
        color: #666;
        min-width: 100px;
    }
    
    .detail-label i {
        width: 20px;
        color: var(--primary);
    }
    
    .detail-value {
        color: #333;
    }
    
    /* Formulario de comentarios */
    .comment-form-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }
    
    .comment-form-card h3 {
        color: var(--dark);
        margin-bottom: 20px;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .comment-form {
        display: flex;
        flex-direction: column;
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
    
    .form-control {
        padding: 12px 15px;
        border: 2px solid #e6e6e6;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.3s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
    }
    
    .form-control.select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23666'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px;
        appearance: none;
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-weight: normal;
    }
    
    .checkbox-label input[type="checkbox"] {
        display: none;
    }
    
    .checkmark {
        width: 20px;
        height: 20px;
        border: 2px solid #e6e6e6;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }
    
    .checkbox-label input[type="checkbox"]:checked + .checkmark {
        background: var(--primary);
        border-color: var(--primary);
    }
    
    .checkbox-label input[type="checkbox"]:checked + .checkmark::after {
        content: '✓';
        color: white;
        font-size: 12px;
    }
    
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 10px;
    }
    
    .required {
        color: var(--warning);
    }
    
    /* Lista de comentarios */
    .comments-list-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .section-header h3 {
        color: var(--dark);
        margin: 0;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .comments-count {
        background: var(--primary);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .comment-item {
        border: 1px solid #e6e6e6;
        border-radius: 10px;
        padding: 20px;
        transition: box-shadow 0.3s;
    }
    
    .comment-item:hover {
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    
    .comment-author-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .author-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
    }
    
    .author-details {
        line-height: 1.3;
    }
    
    .author-name {
        font-weight: 600;
        color: var(--dark);
    }
    
    .comment-type {
        font-size: 12px;
        color: var(--primary);
        background: rgba(67, 97, 238, 0.1);
        padding: 3px 8px;
        border-radius: 12px;
        display: inline-block;
    }
    
    .comment-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }
    
    .comment-date {
        font-size: 12px;
        color: #888;
    }
    
    .comment-actions {
        display: flex;
        gap: 5px;
    }
    
    .comment-content {
        margin-bottom: 15px;
    }
    
    .comment-content p {
        color: #333;
        line-height: 1.6;
        margin: 0;
    }
    
    .comment-footer {
        display: flex;
        justify-content: flex-end;
    }
    
    .comment-status {
        font-size: 12px;
        color: var(--success);
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    /* Botones */
    .btn {
        padding: 10px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--secondary);
    }
    
    .btn-secondary {
        background: #f5f7fb;
        color: #555;
    }
    
    .btn-secondary:hover {
        background: #e6e9ef;
    }
    
    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        font-size: 14px;
    }
    
    .btn-edit {
        background: rgba(76, 201, 240, 0.15);
        color: var(--success);
    }
    
    .btn-edit:hover {
        background: var(--success);
        color: white;
    }
    
    .btn-delete {
        background: rgba(247, 37, 133, 0.15);
        color: var(--warning);
    }
    
    .btn-delete:hover {
        background: var(--warning);
        color: white;
    }
    
    @media (max-width: 768px) {
        .incident-header {
            flex-direction: column;
            gap: 10px;
        }
        
        .incident-details {
            grid-template-columns: 1fr;
        }
        
        .comment-header {
            flex-direction: column;
            gap: 10px;
        }
        
        .comment-meta {
            flex-direction: row;
            justify-content: space-between;
            width: 100%;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
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