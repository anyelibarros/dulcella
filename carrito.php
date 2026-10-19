<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

function productoDisponible(mysqli $mysqli, int $idProducto): ?array
{
    $stmt = $mysqli->prepare(
        'SELECT id_producto, nombre, precio FROM producto WHERE id_producto = ? AND disponible = 1'
    );
    $stmt->bind_param('i', $idProducto);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $idProducto = (int) ($_POST['id_producto'] ?? 0);

    if ($accion === 'agregar' && $idProducto > 0) {
        $cantidad = max(1, (int) ($_POST['cantidad'] ?? 1));
        $prod = productoDisponible($mysqli, $idProducto);
        if ($prod) {
            carritoAgregar($idProducto, $cantidad);
            guardarMensaje('Agregado al carrito: ' . $prod['nombre']);
        } else {
            guardarMensaje('Ese producto ya no está disponible.', 'error');
        }

        if (($_POST['destino'] ?? '') === 'carrito') {
            header('Location: carrito.php');
            exit;
        }
        $volver = (string) ($_POST['volver'] ?? 'catalogo.php');
        if (!preg_match('#^catalogo\.php(\?[a-zA-Z0-9=&_%-]*)?$#', $volver)) {
            $volver = 'catalogo.php';
        }
        header('Location: ' . $volver);
        exit;
    }

    if ($accion === 'actualizar_uno' && $idProducto > 0) {
        carritoFijarCantidad($idProducto, (int) ($_POST['cantidad'] ?? 1));
        guardarMensaje('Carrito actualizado.');
        header('Location: carrito.php');
        exit;
    }

    if ($accion === 'quitar' && $idProducto > 0) {
        carritoFijarCantidad($idProducto, 0);
        guardarMensaje('Producto quitado del carrito.');
        header('Location: carrito.php');
        exit;
    }

    if ($accion === 'vaciar') {
        carritoVaciar();
        guardarMensaje('Carrito vacío. Elige tus postres en el menú.');
        header('Location: catalogo.php');
        exit;
    }
}

$tituloPagina = 'Carrito';
$detalle = carritoDetalle($mysqli);
$items = $detalle['items'];
$total = $detalle['total'];

$disponibles = $mysqli->query(
    'SELECT id_producto, nombre, precio FROM producto WHERE disponible = 1 ORDER BY nombre'
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/header.php';
?>

<nav class="paso-ruta anim-rise" aria-label="Progreso">
  <a href="index.php">Categorías</a>
  <span>→</span>
  <a href="catalogo.php">Menú</a>
  <span>→</span>
  <span class="actual">Carrito</span>
  <span>→</span>
  <span>Pedido</span>
</nav>

<div class="panel-form panel-carrito anim-rise-2">
  <h1>Tu carrito</h1>
  <p class="seccion-sub">Puedes llevar varios postres en un mismo pedido: ajusta cantidades, agrega otros o quita los que no quieras.</p>

  <?php if (!$items): ?>
    <div class="vacio">Tu carrito está vacío.</div>
    <p class="form-volver"><a href="catalogo.php">← Ir al menú para elegir postres</a></p>
  <?php else: ?>
    <div class="carrito-lista">
      <?php foreach ($items as $it): ?>
        <form class="carrito-item" method="post">
          <input type="hidden" name="id_producto" value="<?= (int) $it['id_producto'] ?>">
          <img src="<?= e(urlFoto($it['foto_url'] ?? null)) ?>" alt="<?= e($it['nombre']) ?>">
          <div class="carrito-info">
            <strong><?= e($it['nombre']) ?></strong>
            <span><?= formatearPrecio($it['precio']) ?> c/u</span>
            <span class="carrito-subtotal">= <?= formatearPrecio($it['subtotal']) ?></span>
          </div>
          <label class="carrito-cantidad">
            Cantidad
            <input type="number" name="cantidad" value="<?= (int) $it['cantidad'] ?>" min="0" max="99" inputmode="numeric">
          </label>
          <div class="carrito-acciones">
            <button class="btn btn-choco btn-sm" type="submit" name="accion" value="actualizar_uno">Actualizar</button>
            <button class="btn btn-outline btn-sm" type="submit" name="accion" value="quitar">Quitar</button>
          </div>
        </form>
      <?php endforeach; ?>
    </div>

    <div class="carrito-total">
      <span>Total</span>
      <strong><?= formatearPrecio($total) ?></strong>
    </div>

    <a class="btn btn-rosa btn-block" href="pedido.php">Pedir ya (<?= carritoCantidadTotal() ?> ítem<?= carritoCantidadTotal() === 1 ? '' : 's' ?>)</a>

    <form method="post" onsubmit="return confirm('¿Vaciar todo el carrito?');" style="margin-top:.6rem;">
      <button class="btn btn-outline btn-block" type="submit" name="accion" value="vaciar">Vaciar carrito</button>
    </form>
  <?php endif; ?>

  <?php if ($disponibles): ?>
    <div class="carrito-agregar">
      <h2>Agregar otro postre</h2>
      <form class="formulario" method="post">
        <input type="hidden" name="accion" value="agregar">
        <input type="hidden" name="destino" value="carrito">
        <label for="otro_producto">Producto</label>
        <select id="otro_producto" name="id_producto" required>
          <option value="">Seleccionar…</option>
          <?php foreach ($disponibles as $p): ?>
            <option value="<?= (int) $p['id_producto'] ?>">
              <?= e($p['nombre']) ?> — <?= formatearPrecio($p['precio']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <label for="otra_cantidad">Cantidad</label>
        <input id="otra_cantidad" type="number" name="cantidad" value="1" min="1" max="99">
        <button class="btn btn-rosa btn-block" type="submit">Agregar al carrito</button>
      </form>
      <p class="form-volver"><a href="catalogo.php">← Seguir viendo el menú</a></p>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
