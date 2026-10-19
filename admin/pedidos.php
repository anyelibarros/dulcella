<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereAdmin();

$tituloPagina = 'Bandeja de pedidos';

$pedidos = $mysqli->query(
    'SELECT * FROM pedido ORDER BY fecha DESC, id_pedido DESC'
)->fetch_all(MYSQLI_ASSOC);

$detallesPorPedido = [];
if ($pedidos) {
    $detalles = $mysqli->query(
        'SELECT d.id_pedido, d.cantidad, d.precio_unitario,
                pr.nombre AS producto, c.nombre AS categoria
         FROM pedido_detalle d
         INNER JOIN producto pr ON pr.id_producto = d.id_producto
         INNER JOIN categoria c ON c.id_categoria = pr.id_categoria
         ORDER BY d.id_detalle'
    )->fetch_all(MYSQLI_ASSOC);

    foreach ($detalles as $d) {
        $detallesPorPedido[(int) $d['id_pedido']][] = $d;
    }
}

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
        <?php foreach ($detallesPorPedido[(int) $pe['id_pedido']] ?? [] as $d): ?>
          <p class="producto-linea">
            <?= e($d['producto']) ?> × <?= (int) $d['cantidad'] ?> — <?= formatearPrecio((float) $d['precio_unitario'] * (int) $d['cantidad']) ?>
            <span style="color:var(--muted);font-weight:600;"> · <?= e($d['categoria']) ?> (<?= formatearPrecio($d['precio_unitario']) ?> c/u)</span>
          </p>
        <?php endforeach; ?>
        <p class="producto-linea">Total: <?= formatearPrecio($pe['total']) ?></p>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

</main>
</body>
</html>
