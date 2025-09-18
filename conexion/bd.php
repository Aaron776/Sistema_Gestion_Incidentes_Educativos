<?php
$host = 'localhost';
$dbname = 'sistema_gestion_incidentes_educativos';
$user = 'postgres';
$pass = '1725159683Aron';

try {
    $conexion = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "✅ Conexión a PostgreSQL exitosa!";
} catch (PDOException $e) {
    die("❌ Error al conectar a PostgreSQL: " . $e->getMessage());
}
?>