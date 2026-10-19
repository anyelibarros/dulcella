<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$tituloPagina = 'Pedido';
$error = '';
$idProducto = (int) ($_GET['producto'] ?? $_POST['id_producto'] ?? 0);

$productos = $mysqli->query(
    'SELECT id_producto, nombre, precio, foto_url, descripcion FROM producto WHERE disponible = 1 ORDER BY nombre'
)->fetch_all(MYSQLI_ASSOC);

$productoElegido = null;
if ($idProducto > 0) {
    foreach ($productos as $p) {
        if ((int) $p['id_producto'] === $idProducto) {
            $productoElegido = $p;
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_cliente'] ?? '');
    $telefono = trim($_POST['telefono_cliente'] ?? '');
    $direccion = trim($_POST['direccion_entrega'] ?? '');
    $idProducto = (int) ($_POST['id_producto'] ?? 0);

    if ($nombre === '' || $telefono === '' || $direccion === '' || $idProducto <= 0) {
        $error = 'Completa todos los campos y elige un producto.';
    } else {
        $check = $mysqli->prepare(
            'SELECT id_producto, nombre FROM producto WHERE id_producto = ? AND disponible = 1'
        );
        $check->bind_param('i', $idProducto);
        $check->execute();
        $prod = $check->get_result()->fetch_assoc();

        if (!$prod) {
            $error = 'El producto no está disponible.';
        } else {
            $fecha = date('Y-m-d');
            $ins = $mysqli->prepare(
                'INSERT INTO pedido (nombre_cliente, telefono_cliente, direccion_entrega, fecha, id_producto)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $ins->bind_param('ssssi', $nombre, $telefono, $direccion, $fecha, $idProducto);
            $ins->execute();

            $msgWa = "Hola Dulcella, acabo de pedir: {$prod['nombre']}. Nombre: {$nombre}. Tel: {$telefono}. Dirección: {$direccion}";
            $_SESSION['wa_redirect'] = enlaceWhatsApp($msgWa);
            guardarMensaje('Pedido registrado. Puedes confirmar por WhatsApp.');
            header('Location: gracias.php');
            exit;
        }
    }

    foreach ($productos as $p) {
        if ((int) $p['id_producto'] === $idProducto) {
            $productoElegido = $p;
            break;
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<nav class="paso-ruta anim-rise" aria-label="Progreso">
  <a href="index.php">Categorías</a>
  <span>→</span>
  <a href="catalogo.php">Menú</a>
  <span>→</span>
  <span class="actual">Pedido (1 ítem)</span>
</nav>

<div class="panel-form anim-rise-2">
  <h1>Tu pedido</h1>
  <p class="seccion-sub">Un solo producto por pedido. Te contactamos para coordinar la entrega.</p>

  <?php if ($error): ?>
    <div class="error-box"><?= e($error) ?></div>
  <?php endif; ?>

  <?php if ($productoElegido): ?>
    <div class="producto-resumen">
      <img src="<?= e(urlFoto($productoElegido['foto_url'] ?? null)) ?>" alt="<?= e($productoElegido['nombre']) ?>">
      <div>
        <strong><?= e($productoElegido['nombre']) ?></strong>
        <em><?= formatearPrecio($productoElegido['precio']) ?></em>
      </div>
    </div>
  <?php endif; ?>

  <?php if (!$productos): ?>
    <div class="vacio">No hay productos disponibles para pedir ahora.</div>
  <?php else: ?>
    <form class="formulario" method="post">
      <label for="nombre_cliente">Nombre</label>
      <input id="nombre_cliente" name="nombre_cliente" required maxlength="100"
             value="<?= e($_POST['nombre_cliente'] ?? '') ?>" autocomplete="name">

      <label for="telefono_cliente">Teléfono</label>
      <input id="telefono_cliente" name="telefono_cliente" required maxlength="20"
             value="<?= e($_POST['telefono_cliente'] ?? '') ?>" autocomplete="tel">

      <label for="direccion_entrega">Dirección de entrega</label>
      <input id="direccion_entrega" name="direccion_entrega" required maxlength="255"
             value="<?= e($_POST['direccion_entrega'] ?? '') ?>" autocomplete="street-address">

      <label for="id_producto">Producto</label>
      <select id="id_producto" name="id_producto" required>
        <option value="">Seleccionar…</option>
        <?php foreach ($productos as $p): ?>
          <option value="<?= (int) $p['id_producto'] ?>"
            <?= $idProducto === (int) $p['id_producto'] ? 'selected' : '' ?>>
            <?= e($p['nombre']) ?> — <?= formatearPrecio($p['precio']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button class="btn btn-rosa btn-block" type="submit">Enviar pedido</button>
      <p class="form-volver">
        <a href="catalogo.php">← Volver al menú</a>
      </p>
    </form>
  <?php endif; ?>
</div>

<?php if ($productoElegido): ?>
<script>
  document.getElementById('id_producto')?.addEventListener('change', function () {
    if (this.value) window.location = 'pedido.php?producto=' + encodeURIComponent(this.value);
  });
</script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
