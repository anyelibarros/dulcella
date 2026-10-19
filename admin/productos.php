<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereAdmin();

$tituloPagina = 'Productos';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = (int) ($_POST['id_producto'] ?? 0);

    if ($accion === 'toggle' && $id > 0) {
        $tog = $mysqli->prepare('UPDATE producto SET disponible = IF(disponible = 1, 0, 1) WHERE id_producto = ?');
        $tog->bind_param('i', $id);
        $tog->execute();
        guardarMensaje('Disponibilidad actualizada.');
        header('Location: productos.php');
        exit;
    }

    if ($accion === 'eliminar' && $id > 0) {
        $check = $mysqli->prepare('SELECT COUNT(*) AS c FROM pedido_detalle WHERE id_producto = ?');
        $check->bind_param('i', $id);
        $check->execute();
        if ((int) $check->get_result()->fetch_assoc()['c'] > 0) {
            guardarMensaje('No se puede eliminar: el producto tiene pedidos.', 'error');
        } else {
            $del = $mysqli->prepare('DELETE FROM producto WHERE id_producto = ?');
            $del->bind_param('i', $id);
            $del->execute();
            guardarMensaje('Producto eliminado.');
        }
        header('Location: productos.php');
        exit;
    }
}

$productos = $mysqli->query(
    'SELECT p.*, c.nombre AS categoria
     FROM producto p
     INNER JOIN categoria c ON c.id_categoria = p.id_categoria
     ORDER BY c.nombre, p.nombre'
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/../includes/header_admin.php';
?>

<div class="page-head">
  <div>
    <h1>Gestión de productos</h1>
    <p>Crear, editar, eliminar y marcar disponible.</p>
  </div>
  <a class="btn btn-rosa" href="producto_form.php">+ Nuevo producto</a>
</div>

<div class="tabla-wrap">
  <table>
    <thead>
      <tr>
        <th></th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Categoría</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$productos): ?>
        <tr><td colspan="6">Sin productos. Crea el primero.</td></tr>
      <?php endif; ?>
      <?php foreach ($productos as $p): ?>
        <tr>
          <td>
            <img class="thumb" src="<?= e(urlFoto($p['foto_url'], '../')) ?>" alt="">
          </td>
          <td><?= e($p['nombre']) ?></td>
          <td><?= formatearPrecio($p['precio']) ?></td>
          <td><?= e($p['categoria']) ?></td>
          <td>
            <form class="toggle-form" method="post">
              <input type="hidden" name="accion" value="toggle">
              <input type="hidden" name="id_producto" value="<?= (int) $p['id_producto'] ?>">
              <button class="btn btn-sm <?= (int) $p['disponible'] ? 'btn-choco' : 'btn-outline' ?>" type="submit">
                <?= (int) $p['disponible'] ? 'Disponible' : 'Agotado' ?>
              </button>
            </form>
          </td>
          <td class="acciones">
            <a class="btn btn-outline btn-sm" href="producto_form.php?id=<?= (int) $p['id_producto'] ?>">Editar</a>
            <form method="post" onsubmit="return confirm('¿Eliminar producto?');">
              <input type="hidden" name="accion" value="eliminar">
              <input type="hidden" name="id_producto" value="<?= (int) $p['id_producto'] ?>">
              <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</main>
</body>
</html>
