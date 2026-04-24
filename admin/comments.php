<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
initDB();
requireAdmin();
$pdo = getDB();

if (isset($_GET['approve'])) { $pdo->prepare("UPDATE comments SET approved=1 WHERE id=?")->execute([(int)$_GET['approve']]); redirect(SITE_URL.'/admin/comments.php'); }
if (isset($_GET['delete']))  { $pdo->prepare("DELETE FROM comments WHERE id=?")->execute([(int)$_GET['delete']]);  redirect(SITE_URL.'/admin/comments.php'); }

$comments = $pdo->query("SELECT c.*, p.title AS post_title, p.slug AS post_slug FROM comments c JOIN posts p ON c.post_id = p.id ORDER BY c.created_at DESC")->fetchAll();
$pending  = (int)$pdo->query("SELECT COUNT(*) FROM comments WHERE approved=0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Comentarios · Admin Vallemar</title>
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
.badge{background:var(--red);color:white;border-radius:50px;padding:.1rem .45rem;font-size:.72rem;font-weight:700;margin-left:.2rem}
.admin-content{max-width:1000px;margin:0 auto;padding:2rem 1.5rem}
.page-title{font-family:'Playfair Display',serif;font-size:1.7rem;color:var(--navy);margin-bottom:1.5rem}
.card{background:white;border-radius:12px;box-shadow:0 4px 20px rgba(13,43,78,.09);overflow:hidden}
table{width:100%;border-collapse:collapse}
th,td{padding:.75rem 1rem;text-align:left;border-bottom:1px solid var(--light);font-size:.88rem}
th{background:var(--light);font-weight:700;color:var(--navy);font-size:.8rem;text-transform:uppercase}
.status-badge{font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:50px;display:inline-block}
.status-pending{background:#fef9c3;color:#7d6608}
.status-approved{background:#d4efdf;color:var(--green)}
.action-links{display:flex;gap:.4rem}
.action-links a{font-size:.8rem;font-weight:700;padding:.25rem .65rem;border-radius:6px;text-decoration:none}
.btn-approve{background:var(--green);color:white}
.btn-del{background:var(--red);color:white}
.btn-view{background:var(--navy);color:white}
</style>
</head>
<body>
<nav class="admin-nav">
  <a href="<?= SITE_URL ?>/admin/" class="brand">🏫 Vallemar <span>Admin</span></a>
  <a href="<?= SITE_URL ?>/admin/">Artículos</a>
  <a href="<?= SITE_URL ?>/admin/comments.php">Comentarios<?php if($pending): ?><span class="badge"><?= $pending ?></span><?php endif; ?></a>
  <a href="<?= SITE_URL ?>/admin/categories.php">Categorías</a>
  <a href="<?= SITE_URL ?>/admin/logout.php">Salir</a>
</nav>
<div class="admin-content">
  <h1 class="page-title">💬 Comentarios</h1>
  <div class="card">
    <table>
      <thead><tr><th>Autor</th><th>Comentario</th><th>Artículo</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach ($comments as $c): ?>
        <tr>
          <td><strong><?= e($c['name']) ?></strong><br><span style="color:var(--muted);font-size:.8rem"><?= e($c['email']) ?></span></td>
          <td style="max-width:260px"><?= e(mb_substr($c['body'], 0, 100)) . (mb_strlen($c['body']) > 100 ? '…' : '') ?></td>
          <td style="font-size:.82rem;max-width:160px"><?= e($c['post_title']) ?></td>
          <td><span class="status-badge <?= $c['approved'] ? 'status-approved' : 'status-pending' ?>"><?= $c['approved'] ? 'Aprobado' : 'Pendiente' ?></span></td>
          <td style="white-space:nowrap;font-size:.8rem"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
          <td>
            <div class="action-links">
              <?php if (!$c['approved']): ?>
                <a href="?approve=<?= $c['id'] ?>" class="btn-approve">✓</a>
              <?php endif; ?>
              <a href="<?= SITE_URL ?>/post.php?slug=<?= e($c['post_slug']) ?>#comments" target="_blank" class="btn-view">👁</a>
              <a href="?delete=<?= $c['id'] ?>" class="btn-del" onclick="return confirm('¿Eliminar comentario?')">🗑</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($comments)): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:2rem">No hay comentarios todavía.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body></html>
