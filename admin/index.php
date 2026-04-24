<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
initDB();
requireAdmin();

$pdo = getDB();

// Delete post
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([(int)$_GET['delete']]);
    redirect(SITE_URL . '/admin/');
}

// Toggle publish
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $pdo->prepare("UPDATE posts SET published = 1 - published WHERE id = ?")->execute([(int)$_GET['toggle']]);
    redirect(SITE_URL . '/admin/');
}

$posts = $pdo->query("SELECT p.*, c.name AS cat_name FROM posts p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();
$pendingComments = (int)$pdo->query("SELECT COUNT(*) FROM comments WHERE approved = 0")->fetchColumn();

function adminHeader(string $title): void {
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> · Admin Vallemar</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--navy:#0d2b4e;--blue:#1a5276;--sky:#2e86c1;--gold:#d4a82a;--light:#f0f4f8;--white:#fff;--text:#1c2b3a;--muted:#6b7c8d;--green:#1e8449;--red:#c0392b}
body{font-family:'Nunito',sans-serif;background:var(--light);color:var(--text)}
.admin-nav{background:var(--navy);padding:.75rem 1.5rem;display:flex;align-items:center;gap:1rem;position:sticky;top:0;z-index:100;box-shadow:0 2px 8px rgba(0,0,0,.25)}
.admin-nav .brand{font-family:'Playfair Display',serif;color:var(--white);font-size:1.1rem;text-decoration:none;margin-right:auto}
.admin-nav .brand span{color:var(--gold)}
.admin-nav a{color:rgba(255,255,255,.75);text-decoration:none;padding:.4rem .8rem;border-radius:6px;font-weight:600;font-size:.87rem;transition:all .2s}
.admin-nav a:hover,.admin-nav a.active{background:rgba(255,255,255,.12);color:white}
.admin-nav .badge{background:var(--red);color:white;border-radius:50px;padding:.1rem .45rem;font-size:.72rem;font-weight:700;margin-left:.2rem}
.admin-content{max-width:1100px;margin:0 auto;padding:2rem 1.5rem}
.page-title{font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--navy);margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem}
.page-title a{font-size:1rem;background:var(--gold);color:var(--navy);padding:.4rem 1rem;border-radius:8px;text-decoration:none;font-family:'Nunito',sans-serif;font-weight:700}
.card{background:white;border-radius:12px;box-shadow:0 4px 20px rgba(13,43,78,.09);overflow:hidden;margin-bottom:1.5rem}
.card-header{background:var(--navy);color:white;padding:.8rem 1.2rem;font-weight:700;font-size:.95rem}
table{width:100%;border-collapse:collapse}
th,td{padding:.75rem 1rem;text-align:left;border-bottom:1px solid var(--light);font-size:.9rem}
th{background:var(--light);font-weight:700;color:var(--navy);font-size:.82rem;text-transform:uppercase;letter-spacing:.05em}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafcfe}
.badge-cat{display:inline-block;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:50px;color:white;background:var(--blue)}
.badge-pub{display:inline-block;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:50px}
.badge-pub.yes{background:#d4efdf;color:var(--green)}
.badge-pub.no{background:#fadbd8;color:var(--red)}
.action-links{display:flex;gap:.4rem;flex-wrap:wrap}
.action-links a{font-size:.8rem;font-weight:700;padding:.25rem .65rem;border-radius:6px;text-decoration:none;transition:opacity .2s}
.action-links a:hover{opacity:.8}
.btn-edit{background:var(--sky);color:white}
.btn-toggle{background:var(--gold);color:var(--navy)}
.btn-del{background:var(--red);color:white}
.btn-view{background:var(--navy);color:white}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:2rem}
.stat-card{background:white;border-radius:12px;padding:1.2rem 1.4rem;box-shadow:0 4px 20px rgba(13,43,78,.09);border-left:4px solid var(--blue)}
.stat-card .stat-num{font-family:'Playfair Display',serif;font-size:2rem;color:var(--navy);line-height:1}
.stat-card .stat-label{font-size:.82rem;color:var(--muted);margin-top:.3rem;font-weight:600}
.alert{padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-weight:600;font-size:.9rem}
.alert-success{background:#d4efdf;color:var(--green)}
.alert-error{background:#fadbd8;color:var(--red)}
</style>
</head>
<body>
<nav class="admin-nav">
  <a href="<?= SITE_URL ?>/admin/" class="brand">🏫 Vallemar <span>Admin</span></a>
  <a href="<?= SITE_URL ?>/admin/" class="active">Artículos</a>
  <a href="<?= SITE_URL ?>/admin/comments.php">Comentarios<?php global $pendingComments; if($pendingComments>0) echo '<span class="badge">'.$pendingComments.'</span>'; ?></a>
  <a href="<?= SITE_URL ?>/admin/categories.php">Categorías</a>
  <a href="<?= SITE_URL ?>" target="_blank">Ver Blog</a>
  <a href="<?= SITE_URL ?>/admin/logout.php">Salir</a>
</nav>
<div class="admin-content">
<?php
}

adminHeader('Dashboard');
?>

<!-- STATS -->
<div class="stats-grid">
  <?php
  $totalPosts = (int)$pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
  $pubPosts   = (int)$pdo->query("SELECT COUNT(*) FROM posts WHERE published=1")->fetchColumn();
  $totalComs  = (int)$pdo->query("SELECT COUNT(*) FROM comments WHERE approved=1")->fetchColumn();
  $totalCats  = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
  ?>
  <div class="stat-card" style="border-color:var(--navy)">
    <div class="stat-num"><?= $totalPosts ?></div>
    <div class="stat-label">Artículos totales</div>
  </div>
  <div class="stat-card" style="border-color:var(--green)">
    <div class="stat-num"><?= $pubPosts ?></div>
    <div class="stat-label">Publicados</div>
  </div>
  <div class="stat-card" style="border-color:var(--sky)">
    <div class="stat-num"><?= $totalComs ?></div>
    <div class="stat-label">Comentarios</div>
  </div>
  <div class="stat-card" style="border-color:var(--gold)">
    <div class="stat-num"><?= $pendingComments ?></div>
    <div class="stat-label">Pendientes revisión</div>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?= $totalCats ?></div>
    <div class="stat-label">Categorías</div>
  </div>
</div>

<div class="page-title">
  Artículos
  <a href="<?= SITE_URL ?>/admin/edit-post.php">+ Nuevo artículo</a>
</div>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Título</th>
        <th>Categoría</th>
        <th>Estado</th>
        <th>Fecha</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($posts as $p): ?>
      <tr>
        <td><strong><?= e($p['title']) ?></strong></td>
        <td><?= $p['cat_name'] ? '<span class="badge-cat">' . e($p['cat_name']) . '</span>' : '<span style="color:var(--muted)">—</span>' ?></td>
        <td><span class="badge-pub <?= $p['published'] ? 'yes' : 'no' ?>"><?= $p['published'] ? 'Publicado' : 'Borrador' ?></span></td>
        <td style="white-space:nowrap"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
        <td>
          <div class="action-links">
            <a href="edit-post.php?id=<?= $p['id'] ?>" class="btn-edit">✏ Editar</a>
            <a href="?toggle=<?= $p['id'] ?>" class="btn-toggle" onclick="return confirm('¿Cambiar estado?')"><?= $p['published'] ? '⊘ Ocultar' : '✓ Publicar' ?></a>
            <a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>" target="_blank" class="btn-view">👁</a>
            <a href="?delete=<?= $p['id'] ?>" class="btn-del" onclick="return confirm('¿Eliminar este artículo?')">🗑</a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($posts)): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:2rem">No hay artículos todavía. <a href="edit-post.php">Crea el primero</a>.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</div></body></html>
