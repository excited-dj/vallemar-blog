<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
initDB();
requireAdmin();
$pdo = getDB();
$msg = ''; $err = '';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_GET['delete']]);
    redirect(SITE_URL.'/admin/categories.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $color = trim($_POST['color'] ?? '#1a5276');
    $catSlug = slug($name);
    if (!$name) { $err = 'El nombre es obligatorio.'; }
    else {
        $pdo->prepare("INSERT IGNORE INTO categories (name,slug,color) VALUES (?,?,?)")->execute([$name,$catSlug,$color]);
        $msg = 'Categoría añadida.';
    }
}

$cats = getCategories();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Categorías · Admin Vallemar</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--navy:#0d2b4e;--blue:#1a5276;--sky:#2e86c1;--gold:#d4a82a;--light:#f0f4f8;--green:#1e8449;--red:#c0392b;--muted:#6b7c8d}
body{font-family:'Nunito',sans-serif;background:var(--light);color:#1c2b3a}
.admin-nav{background:var(--navy);padding:.75rem 1.5rem;display:flex;align-items:center;gap:1rem;box-shadow:0 2px 8px rgba(0,0,0,.25)}
.admin-nav .brand{font-family:'Playfair Display',serif;color:white;font-size:1.1rem;text-decoration:none;margin-right:auto}
.admin-nav .brand span{color:var(--gold)}
.admin-nav a{color:rgba(255,255,255,.75);text-decoration:none;padding:.4rem .8rem;border-radius:6px;font-weight:600;font-size:.87rem}
.admin-nav a:hover{background:rgba(255,255,255,.12);color:white}
.admin-content{max-width:700px;margin:0 auto;padding:2rem 1.5rem}
.page-title{font-family:'Playfair Display',serif;font-size:1.7rem;color:var(--navy);margin-bottom:1.5rem}
.card{background:white;border-radius:12px;box-shadow:0 4px 20px rgba(13,43,78,.09);padding:1.5rem;margin-bottom:1.5rem}
.form-row{display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap}
.form-group{display:flex;flex-direction:column;gap:.35rem;flex:1;min-width:160px}
.form-group label{font-weight:700;font-size:.85rem;color:var(--navy)}
.form-group input{border:1.5px solid #c8d6e3;border-radius:8px;padding:.65rem .9rem;font-family:'Nunito',sans-serif;font-size:.95rem;outline:none;transition:border .2s}
.form-group input:focus{border-color:var(--sky)}
.btn{background:var(--gold);color:var(--navy);border:none;border-radius:8px;padding:.7rem 1.5rem;font-family:'Nunito',sans-serif;font-weight:700;font-size:.95rem;cursor:pointer}
.alert{padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-weight:600;font-size:.9rem}
.alert-success{background:#d4efdf;color:var(--green)}
.alert-error{background:#fadbd8;color:var(--red)}
table{width:100%;border-collapse:collapse}
th,td{padding:.75rem 1rem;text-align:left;border-bottom:1px solid var(--light);font-size:.9rem}
th{background:var(--light);font-weight:700;font-size:.8rem;color:var(--navy);text-transform:uppercase}
.del-btn{font-size:.8rem;font-weight:700;padding:.25rem .65rem;border-radius:6px;text-decoration:none;background:var(--red);color:white}
</style>
</head>
<body>
<nav class="admin-nav">
  <a href="<?= SITE_URL ?>/admin/" class="brand">🏫 Vallemar <span>Admin</span></a>
  <a href="<?= SITE_URL ?>/admin/">Artículos</a>
  <a href="<?= SITE_URL ?>/admin/comments.php">Comentarios</a>
  <a href="<?= SITE_URL ?>/admin/categories.php">Categorías</a>
  <a href="<?= SITE_URL ?>/admin/logout.php">Salir</a>
</nav>
<div class="admin-content">
  <h1 class="page-title">🏷 Categorías</h1>
  <?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

  <div class="card">
    <h3 style="margin-bottom:1rem;color:var(--navy)">Nueva categoría</h3>
    <form method="post">
      <div class="form-row">
        <div class="form-group">
          <label>Nombre</label>
          <input type="text" name="name" required placeholder="Ej: Actividades">
        </div>
        <div class="form-group" style="flex:0 0 100px">
          <label>Color</label>
          <input type="color" name="color" value="#1a5276" style="height:44px;padding:.2rem .3rem">
        </div>
        <button type="submit" class="btn">+ Añadir</button>
      </div>
    </form>
  </div>

  <div class="card" style="padding:0">
    <table>
      <thead><tr><th>Color</th><th>Nombre</th><th>Slug</th><th>Artículos</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($cats as $c): ?>
        <tr>
          <td><span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:<?= e($c['color']) ?>"></span></td>
          <td><strong><?= e($c['name']) ?></strong></td>
          <td style="color:var(--muted);font-size:.85rem"><?= e($c['slug']) ?></td>
          <td><?= $c['post_count'] ?></td>
          <td><a href="?delete=<?= $c['id'] ?>" class="del-btn" onclick="return confirm('¿Eliminar categoría?')">🗑</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body></html>
