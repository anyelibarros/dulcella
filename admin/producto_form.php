<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereAdmin();

$id = (int) ($_GET['id'] ?? $_POST['id_producto'] ?? 0);
$tituloPagina = $id ? 'Editar producto' : 'Nuevo producto';
$error = '';

$producto = [
    'nombre' => '',
    'precio' => '',
    'descripcion' => '',
    'disponible' => 1,
    'foto_url' => '',
    'id_categoria' => 0,
];

if ($id > 0 && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $st = $mysqli->prepare('SELECT * FROM producto WHERE id_producto = ?');
    $st->bind_param('i', $id);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    if (!$row) {
        guardarMensaje('Producto no encontrado.', 'error');
        header('Location: productos.php');
        exit;
    }
    $producto = $row;
}

$categorias = $mysqli->query('SELECT id_categoria, nombre FROM categoria ORDER BY nombre')
    ->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = (float) str_replace(',', '.', $_POST['precio'] ?? '0');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    $foto = trim($_POST['foto_url'] ?? '');
    $idCat = (int) ($_POST['id_categoria'] ?? 0);

    $producto = [
        'nombre' => $nombre,
        'precio' => $precio,
        'descripcion' => $descripcion,
        'disponible' => $disponible,
        'foto_url' => $foto,
        'id_categoria' => $idCat,
    ];

    if ($nombre === '' || $precio <= 0 || $idCat <= 0) {
        $error = 'Nombre, precio válido y categoría son obligatorios.';
    } else {
        if ($id > 0) {
            $up = $mysqli->prepare(
                'UPDATE producto SET nombre=?, precio=?, descripcion=?, disponible=?, foto_url=?, id_categoria=?
                 WHERE id_producto=?'
            );
            $up->bind_param('sdsisii', $nombre, $precio, $descripcion, $disponible, $foto, $idCat, $id);
            $up->execute();
            guardarMensaje('Producto actualizado.');
        } else {
            $ins = $mysqli->prepare(
                'INSERT INTO producto (nombre, precio, descripcion, disponible, foto_url, id_categoria)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $ins->bind_param('sdsisi', $nombre, $precio, $descripcion, $disponible, $foto, $idCat);
            $ins->execute();
            guardarMensaje('Producto creado.');
        }
        header('Location: productos.php');
        exit;
    }
}

require __DIR__ . '/../includes/header_admin.php';
?>

<div class="page-head">
  <div>
    <h1><?= e($tituloPagina) ?></h1>
    <p><a href="productos.php">‹ Volver a Gestión de productos</a></p>
  </div>
</div>

<div class="panel-form">
  <?php if ($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>
  <?php if (!$categorias): ?>
    <div class="vacio">Primero crea al menos una categoría.</div>
  <?php else: ?>
    <form class="formulario" method="post">
      <input type="hidden" name="id_producto" value="<?= $id ?>">
      <label for="nombre">Nombre</label>
      <input id="nombre" name="nombre" required maxlength="150" value="<?= e((string) $producto['nombre']) ?>">

      <label for="precio">Precio</label>
      <input id="precio" name="precio" type="number" step="100" min="100" required
             value="<?= e((string) $producto['precio']) ?>">

      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion"><?= e((string) ($producto['descripcion'] ?? '')) ?></textarea>

      <label for="foto_url">Foto (ruta local o URL)</label>
      <input id="foto_url" name="foto_url" maxlength="255" value="<?= e((string) ($producto['foto_url'] ?? '')) ?>"
             placeholder="img/chocolate/ejemplo.jpg">
      <?php if (!empty($producto['foto_url'])): ?>
        <p style="margin-top:0.5rem;"><img class="thumb" src="<?= e(urlFoto($producto['foto_url'], '../')) ?>" alt=""></p>
      <?php endif; ?>

      <label for="id_categoria">Categoría</label>
      <select id="id_categoria" name="id_categoria" required>
        <option value="">Seleccionar…</option>
        <?php foreach ($categorias as $c): ?>
          <option value="<?= (int) $c['id_categoria'] ?>"
            <?= (int) $producto['id_categoria'] === (int) $c['id_categoria'] ? 'selected' : '' ?>>
            <?= e($c['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label style="display:flex;align-items:center;gap:0.5rem;margin-top:1rem;">
        <input type="checkbox" name="disponible" value="1" <?= (int) $producto['disponible'] ? 'checked' : '' ?>>
        Disponible
      </label>

      <button class="btn btn-rosa" type="submit" style="width:100%;margin-top:1.2rem;">Guardar</button>
    </form>
  <?php endif; ?>
</div>

</main>
</body>
</html>
