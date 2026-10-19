<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$tituloPagina = 'Menú';
$idCat = (int) ($_GET['categoria'] ?? 0);
$buscar = trim($_GET['q'] ?? '');

$categorias = $mysqli->query('SELECT id_categoria, nombre FROM categoria ORDER BY id_categoria')
    ->fetch_all(MYSQLI_ASSOC);

$catNombre = '';
if ($idCat > 0) {
    $stmtCat = $mysqli->prepare('SELECT nombre FROM categoria WHERE id_categoria = ?');
    $stmtCat->bind_param('i', $idCat);
    $stmtCat->execute();
    $catNombre = (string) ($stmtCat->get_result()->fetch_assoc()['nombre'] ?? '');
}

$sql = 'SELECT p.*, c.nombre AS categoria
        FROM producto p
        INNER JOIN categoria c ON c.id_categoria = p.id_categoria
        WHERE p.disponible = 1';
$types = '';
$params = [];

if ($idCat > 0) {
    $sql .= ' AND p.id_categoria = ?';
    $types .= 'i';
    $params[] = $idCat;
}
if ($buscar !== '') {
    $sql .= ' AND (p.nombre LIKE ? OR p.descripcion LIKE ?)';
    $like = '%' . $buscar . '%';
    $types .= 'ss';
    $params[] = $like;
    $params[] = $like;
}
$sql .= ' ORDER BY c.id_categoria, p.nombre';

$stmt = $mysqli->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/header.php';
?>

<nav class="paso-ruta anim-rise" aria-label="Progreso">
  <a href="index.php">Categorías</a>
  <span>→</span>
  <span class="actual"><?= $catNombre !== '' ? e($catNombre) : 'Menú' ?></span>
  <span>→</span>
  <span>Carrito</span>
  <span>→</span>
  <span>Pedido</span>
</nav>

<h1 class="seccion-titulo anim-rise">Menú Dulcella</h1>
<p class="seccion-sub anim-rise-2">Agrega todos los dulces que quieras al carrito y pide en una sola orden. Coordinamos la entrega por WhatsApp.</p>

<?php if ($catNombre !== ''): ?>
  <div class="filtro-activo">Viendo: <?= e($catNombre) ?> · <a href="catalogo.php">Ver todas</a></div>
<?php endif; ?>

<form class="toolbar anim-rise-2" method="get">
  <input type="search" name="q" placeholder="Buscar postre o bebida…" value="<?= e($buscar) ?>">
  <select name="categoria" onchange="this.form.submit()">
    <option value="">Todas las categorías</option>
    <?php foreach ($categorias as $c): ?>
      <option value="<?= (int) $c['id_categoria'] ?>" <?= $idCat === (int) $c['id_categoria'] ? 'selected' : '' ?>>
        <?= e($c['nombre']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <button class="btn btn-choco" type="submit">Buscar</button>
</form>

<?php if (!$productos): ?>
  <div class="vacio">No hay productos disponibles con ese filtro.</div>
<?php else: ?>
  <div class="grid-productos">
    <?php foreach ($productos as $p): ?>
      <?php
        $msgWa = 'Hola Dulcella, me interesa: ' . $p['nombre'] . ' (' . formatearPrecio($p['precio']) . ')';
        $foto = urlFoto($p['foto_url']);
      ?>
      <article class="card-producto">
        <div class="media">
          <img src="<?= e($foto) ?>" alt="<?= e($p['nombre']) ?>" loading="lazy">
          <span class="tag-cat"><?= e($p['categoria']) ?></span>
        </div>
        <div class="card-body">
          <h3><?= e($p['nombre']) ?></h3>
          <div class="precio"><?= formatearPrecio($p['precio']) ?></div>
          <p><?= e($p['descripcion'] ?: $p['categoria']) ?></p>
          <div class="card-acciones">
            <form method="post" action="carrito.php">
              <input type="hidden" name="accion" value="agregar">
              <input type="hidden" name="id_producto" value="<?= (int) $p['id_producto'] ?>">
              <input type="hidden" name="volver" value="catalogo.php<?= $_SERVER['QUERY_STRING'] ? '?' . e($_SERVER['QUERY_STRING']) : '' ?>">
              <button class="btn btn-rosa btn-sm" type="submit">Agregar al carrito</button>
            </form>
            <form method="post" action="carrito.php">
              <input type="hidden" name="accion" value="agregar">
              <input type="hidden" name="destino" value="carrito">
              <input type="hidden" name="id_producto" value="<?= (int) $p['id_producto'] ?>">
              <button class="btn btn-choco btn-sm" type="submit">Pedir ya</button>
            </form>
            <a class="btn btn-wa btn-sm" href="<?= e(enlaceWhatsApp($msgWa)) ?>" target="_blank" rel="noopener">WhatsApp</a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
