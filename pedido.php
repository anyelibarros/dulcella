<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$tituloPagina = 'Pedido';
$error = '';

// Compatibilidad: enlaces antiguos "Pedir este" suman el producto al carrito.
if (isset($_GET['producto'])) {
    $idProducto = (int) $_GET['producto'];
    if ($idProducto > 0) {
        $stmt = $mysqli->prepare('SELECT id_producto FROM producto WHERE id_producto = ? AND disponible = 1');
        $stmt->bind_param('i', $idProducto);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            carritoAgregar($idProducto, 1);
        }
    }
    header('Location: pedido.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_cliente'] ?? '');
    $telefono = trim($_POST['telefono_cliente'] ?? '');
    $direccion = trim($_POST['direccion_entrega'] ?? '');
    $detalle = carritoDetalle($mysqli);

    if ($nombre === '' || $telefono === '' || $direccion === '') {
        $error = 'Completa todos tus datos para poder entregar el pedido.';
    } elseif (!$detalle['items']) {
        $error = 'Tu carrito está vacío. Agrega al menos un postre desde el menú.';
    } else {
        $total = $detalle['total'];
        $fecha = date('Y-m-d');
        $idPedido = 0;

        try {
            $mysqli->begin_transaction();

            $ins = $mysqli->prepare(
                'INSERT INTO pedido (nombre_cliente, telefono_cliente, direccion_entrega, fecha, total)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $ins->bind_param('ssssd', $nombre, $telefono, $direccion, $fecha, $total);
            $ins->execute();
            $idPedido = (int) $mysqli->insert_id;

            $insDet = $mysqli->prepare(
                'INSERT INTO pedido_detalle (id_pedido, id_producto, cantidad, precio_unitario)
                 VALUES (?, ?, ?, ?)'
            );
            foreach ($detalle['items'] as $it) {
                $idProd = (int) $it['id_producto'];
                $cantidad = (int) $it['cantidad'];
                $precio = (float) $it['precio'];
                $insDet->bind_param('iiid', $idPedido, $idProd, $cantidad, $precio);
                $insDet->execute();
            }

            $mysqli->commit();
        } catch (Throwable $e) {
            $mysqli->rollback();
            $error = 'No se pudo registrar el pedido. Intenta de nuevo.';
        }

        if ($error === '') {
            $lineas = ["Hola Dulcella, acabo de hacer un pedido (#{$idPedido}):"];
            foreach ($detalle['items'] as $it) {
                $lineas[] = "- {$it['nombre']} x{$it['cantidad']} — " . formatearPrecio($it['subtotal']);
            }
            $lineas[] = 'Total: ' . formatearPrecio($total);
            $lineas[] = "Nombre: {$nombre}";
            $lineas[] = "Tel: {$telefono}";
            $lineas[] = "Dirección: {$direccion}";

            carritoVaciar();
            $_SESSION['wa_redirect'] = enlaceWhatsApp(implode("\n", $lineas));
            guardarMensaje('Pedido registrado. Puedes confirmar por WhatsApp.');
            header('Location: gracias.php');
            exit;
        }
    }
}

$detalle = carritoDetalle($mysqli);
$items = $detalle['items'];
$total = $detalle['total'];

require __DIR__ . '/includes/header.php';
?>

<nav class="paso-ruta anim-rise" aria-label="Progreso">
  <a href="index.php">Categorías</a>
  <span>→</span>
  <a href="catalogo.php">Menú</a>
  <span>→</span>
  <a href="carrito.php">Carrito</a>
  <span>→</span>
  <span class="actual">Pedido</span>
</nav>

<div class="panel-form anim-rise-2">
  <h1>Confirma tu pedido</h1>
  <p class="seccion-sub">Revisa tus postres, deja tus datos y te contactamos para coordinar la entrega.</p>

  <?php if ($error): ?>
    <div class="error-box"><?= e($error) ?></div>
  <?php endif; ?>

  <?php if (!$items): ?>
    <div class="vacio">Tu carrito está vacío. Todavía no hay nada para pedir.</div>
    <p class="form-volver">
      <a class="btn btn-rosa" href="catalogo.php">Ver el menú</a>
    </p>
  <?php else: ?>
    <div class="resumen-carrito">
      <?php foreach ($items as $it): ?>
        <div class="resumen-linea">
          <span><?= e($it['nombre']) ?> <em>× <?= (int) $it['cantidad'] ?></em></span>
          <strong><?= formatearPrecio($it['subtotal']) ?></strong>
        </div>
      <?php endforeach; ?>
      <div class="resumen-linea resumen-total">
        <span>Total</span>
        <strong><?= formatearPrecio($total) ?></strong>
      </div>
      <p class="form-volver" style="text-align:left;">
        <a href="carrito.php">← Editar carrito (agregar, quitar o cambiar cantidades)</a>
      </p>
    </div>

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

      <button class="btn btn-rosa btn-block" type="submit">Enviar pedido</button>
      <p class="form-volver">
        <a href="catalogo.php">← Volver al menú</a>
      </p>
    </form>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
