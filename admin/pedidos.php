<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereAdmin();

$tituloPagina = 'Bandeja de pedidos';

$pedidos = $mysqli->query(
    'SELECT pe.*, pr.nombre AS producto, pr.precio, pr.foto_url, c.nombre AS categoria
     FROM pedido pe
     INNER JOIN producto pr ON pr.id_producto = pe.id_producto
     INNER JOIN categoria c ON c.id_categoria = pr.id_categoria
     ORDER BY pe.fecha DESC, pe.id_pedido DESC'
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/../includes/header_admin.php';
?>

<div class="page-head">
  <div>
    <h1>Bandeja de pedidos</h1>
    <p><?= count($pedidos) ?> pedido<?= count($pedidos) === 1 ? '' : 's' ?> · más recientes primero.</p>
  </div>
  <a class="btn btn-outline" href="../index.php" target="_blank" rel="noopener">Ver sitio</a>
</div>

<?php if (!$pedidos): ?>
  <div class="vacio">Aún no hay pedidos.</div>
<?php else: ?>
  <div class="bandeja">
    <?php foreach ($pedidos as $pe): ?>
      <article class="bandeja-item">
        <div class="bandeja-top">
          <strong>#<?= (int) $pe['id_pedido'] ?> · <?= e($pe['nombre_cliente']) ?></strong>
          <span><?= e(date('d/m/Y', strtotime($pe['fecha']))) ?></span>
        </div>
        <p>Tel: <?= e($pe['telefono_cliente']) ?> · <?= e($pe['direccion_entrega']) ?></p>
        <p class="producto-linea">
          <?= e($pe['producto']) ?> — <?= formatearPrecio($pe['precio']) ?>
          <span style="color:var(--muted);font-weight:600;"> · <?= e($pe['categoria']) ?></span>
        </p>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

</main>
</body>
</html>
