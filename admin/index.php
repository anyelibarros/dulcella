<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereAdmin();

$tituloPagina = 'Panel principal';
$totalCat = (int) $mysqli->query('SELECT COUNT(*) AS c FROM categoria')->fetch_assoc()['c'];
$totalProd = (int) $mysqli->query('SELECT COUNT(*) AS c FROM producto')->fetch_assoc()['c'];
$totalPed = (int) $mysqli->query('SELECT COUNT(*) AS c FROM pedido')->fetch_assoc()['c'];

require __DIR__ . '/../includes/header_admin.php';
?>

<div class="page-head">
  <div>
    <h1>Panel principal</h1>
    <p>Resumen de Dulcella · Hola, <?= e($_SESSION['admin_nombre'] ?? 'admin') ?></p>
  </div>
</div>

<div class="metricas">
  <div class="metrica"><span>Categorías</span><strong><?= $totalCat ?></strong></div>
  <div class="metrica"><span>Productos</span><strong><?= $totalProd ?></strong></div>
  <div class="metrica"><span>Pedidos</span><strong><?= $totalPed ?></strong></div>
</div>

<p class="admin-atajos">
  <a class="btn btn-rosa" href="categorias.php">Gestionar categorías</a>
  <a class="btn btn-choco" href="productos.php">Gestionar productos</a>
  <a class="btn btn-outline" href="pedidos.php">Ver pedidos</a>
</p>

</main>
</body>
</html>
