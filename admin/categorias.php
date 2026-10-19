<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
requiereAdmin();

$tituloPagina = 'Categorías';
$error = '';
$editId = (int) ($_GET['editar'] ?? 0);
$editNombre = '';

if ($editId > 0) {
    $st = $mysqli->prepare('SELECT nombre FROM categoria WHERE id_categoria = ?');
    $st->bind_param('i', $editId);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    if ($row) {
        $editNombre = $row['nombre'];
    } else {
        $editId = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'guardar') {
        $nombre = trim($_POST['nombre'] ?? '');
        $id = (int) ($_POST['id_categoria'] ?? 0);
        if ($nombre === '') {
            $error = 'El nombre es obligatorio.';
        } else {
            if ($id > 0) {
                $up = $mysqli->prepare('UPDATE categoria SET nombre = ? WHERE id_categoria = ?');
                $up->bind_param('si', $nombre, $id);
                $up->execute();
                guardarMensaje('Categoría actualizada.');
            } else {
                $ins = $mysqli->prepare('INSERT INTO categoria (nombre) VALUES (?)');
                $ins->bind_param('s', $nombre);
                $ins->execute();
                guardarMensaje('Categoría creada.');
            }
            header('Location: categorias.php');
            exit;
        }
    }

    if ($accion === 'eliminar') {
        $id = (int) ($_POST['id_categoria'] ?? 0);
        $check = $mysqli->prepare('SELECT COUNT(*) AS c FROM producto WHERE id_categoria = ?');
        $check->bind_param('i', $id);
        $check->execute();
        $tiene = (int) $check->get_result()->fetch_assoc()['c'];
        if ($tiene > 0) {
            guardarMensaje('No se puede eliminar: tiene productos asociados.', 'error');
        } else {
            $del = $mysqli->prepare('DELETE FROM categoria WHERE id_categoria = ?');
            $del->bind_param('i', $id);
            $del->execute();
            guardarMensaje('Categoría eliminada.');
        }
        header('Location: categorias.php');
        exit;
    }
}

$categorias = $mysqli->query(
    'SELECT c.*, COUNT(p.id_producto) AS productos
     FROM categoria c
     LEFT JOIN producto p ON p.id_categoria = c.id_categoria
     GROUP BY c.id_categoria
     ORDER BY c.nombre'
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/../includes/header_admin.php';
?>

<div class="page-head">
  <div>
    <h1>Gestión de categorías</h1>
    <p>Crear, editar y eliminar categorías del menú.</p>
  </div>
</div>

<div class="panel-form" style="margin-bottom:1.5rem;">
  <h2 style="color:var(--choco);margin-bottom:0.6rem;"><?= $editId ? 'Editar categoría' : 'Nueva categoría' ?></h2>
  <?php if ($error): ?><div class="error-box"><?= e($error) ?></div><?php endif; ?>
  <form class="formulario" method="post">
    <input type="hidden" name="accion" value="guardar">
    <input type="hidden" name="id_categoria" value="<?= $editId ?>">
    <label for="nombre">Nombre</label>
    <input id="nombre" name="nombre" required maxlength="100"
           value="<?= e($_POST['nombre'] ?? $editNombre) ?>">
    <div class="admin-atajos" style="margin-top:1rem;">
      <button class="btn btn-rosa" type="submit"><?= $editId ? 'Actualizar' : 'Crear' ?></button>
      <?php if ($editId): ?>
        <a class="btn btn-outline" href="categorias.php">Cancelar</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<div class="tabla-wrap">
  <table>
    <thead>
      <tr><th>Nombre</th><th>Productos</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php if (!$categorias): ?>
        <tr><td colspan="3">Sin categorías aún.</td></tr>
      <?php endif; ?>
      <?php foreach ($categorias as $c): ?>
        <tr>
          <td><?= e($c['nombre']) ?></td>
          <td><?= (int) $c['productos'] ?></td>
          <td class="acciones">
            <a class="btn btn-outline btn-sm" href="categorias.php?editar=<?= (int) $c['id_categoria'] ?>">Editar</a>
            <form method="post" onsubmit="return confirm('¿Eliminar esta categoría?');">
              <input type="hidden" name="accion" value="eliminar">
              <input type="hidden" name="id_categoria" value="<?= (int) $c['id_categoria'] ?>">
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
