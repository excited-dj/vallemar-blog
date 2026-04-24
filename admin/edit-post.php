<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
initDB();
requireAdmin();

$pdo  = getDB();
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $id ? getPost((string)$id, true) : null;
$cats = getCategories();
$msg  = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $body     = trim($_POST['body']  ?? '');
    $excerpt  = trim($_POST['excerpt'] ?? '');
    $author   = trim($_POST['author'] ?? 'Colegio Vallemar');
    $catId    = (int)($_POST['category_id'] ?? 0) ?: null;
    $published = isset($_POST['published']) ? 1 : 0;
    $postSlug  = trim($_POST['slug'] ?? '') ?: slug($title);

    if (!$title || !$body) {
        $err = 'El título y el contenido son obligatorios.';
    } else {
        // Handle image upload
        $image = $post['image'] ?? null;
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $fname = uniqid('img_') . '.' . $ext;
                $dest  = __DIR__ . '/../uploads/' . $fname;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $image = $fname;
                }
            }
        }

        if ($id && $post) {
            $stmt = $pdo->prepare("UPDATE posts SET title=?,slug=?,excerpt=?,body=?,author=?,category_id=?,published=?,image=? WHERE id=?");
            $stmt->execute([$title, $postSlug, $excerpt, $body, $author, $catId, $published, $image, $id]);
            $msg = 'Artículo actualizado correctamente.';
            $post = getPost((string)$id, true);
        } else {
            // Ensure unique slug
            $base = $postSlug; $i = 1;
            while ($pdo->prepare("SELECT id FROM posts WHERE slug=?")->execute([$postSlug]) && $pdo->query("SELECT id FROM posts WHERE slug='$postSlug'")->fetchColumn()) {
                $postSlug = $base . '-' . $i++;
            }
            $stmt = $pdo->prepare("INSERT INTO posts (title,slug,excerpt,body,author,category_id,published,image) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$title, $postSlug, $excerpt, $body, $author, $catId, $published, $image]);
            $id   = (int)$pdo->lastInsertId();
            $post = getPost((string)$id, true);
            $msg  = 'Artículo creado correctamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $id ? 'Editar' : 'Nuevo' ?> Artículo · Admin Vallemar</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--navy:#0d2b4e;--blue:#1a5276;--sky:#2e86c1;--gold:#d4a82a;--light:#f0f4f8;--white:#fff;--text:#1c2b3a;--muted:#6b7c8d;--green:#1e8449;--red:#c0392b}
body{font-family:'Nunito',sans-serif;background:var(--light);color:var(--text)}
.admin-nav{background:var(--navy);padding:.75rem 1.5rem;display:flex;align-items:center;gap:1rem;box-shadow:0 2px 8px rgba(0,0,0,.25)}
.admin-nav .brand{font-family:'Playfair Display',serif;color:white;font-size:1.1rem;text-decoration:none;margin-right:auto}
.admin-nav .brand span{color:var(--gold)}
.admin-nav a{color:rgba(255,255,255,.75);text-decoration:none;padding:.4rem .8rem;border-radius:6px;font-weight:600;font-size:.87rem}
.admin-nav a:hover{background:rgba(255,255,255,.12);color:white}
.admin-content{max-width:900px;margin:0 auto;padding:2rem 1.5rem}
.page-title{font-family:'Playfair Display',serif;font-size:1.7rem;color:var(--navy);margin-bottom:1.5rem}
.card{background:white;border-radius:12px;box-shadow:0 4px 20px rgba(13,43,78,.09);padding:2rem;margin-bottom:1.5rem}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
@media(max-width:600px){.form-row{grid-template-columns:1fr}}
.form-group{display:flex;flex-direction:column;gap:.35rem;margin-bottom:1rem}
.form-group label{font-weight:700;font-size:.85rem;color:var(--navy)}
.form-group input,.form-group select,.form-group textarea{border:1.5px solid #c8d6e3;border-radius:8px;padding:.65rem .9rem;font-family:'Nunito',sans-serif;font-size:.95rem;outline:none;transition:border .2s}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--sky)}
.form-group textarea{resize:vertical;min-height:340px}
.form-hint{font-size:.78rem;color:var(--muted);margin-top:.2rem}
.btn{background:var(--navy);color:white;border:none;border-radius:8px;padding:.7rem 1.8rem;font-family:'Nunito',sans-serif;font-weight:700;font-size:.95rem;cursor:pointer;transition:background .2s}
.btn:hover{background:var(--blue)}
.btn-gold{background:var(--gold);color:var(--navy)}
.btn-gold:hover{background:#e8b830}
.alert{padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-weight:600;font-size:.9rem}
.alert-success{background:#d4efdf;color:var(--green)}
.alert-error{background:#fadbd8;color:var(--red)}
.check-group{display:flex;align-items:center;gap:.5rem;cursor:pointer}
.check-group input[type=checkbox]{width:18px;height:18px;cursor:pointer;accent-color:var(--blue)}
.actions{display:flex;gap:.75rem;align-items:center;flex-wrap:wrap}
.back-link{color:var(--blue);text-decoration:none;font-weight:600;font-size:.9rem}
</style>
</head>
<body>
<nav class="admin-nav">
  <a href="<?= SITE_URL ?>/admin/" class="brand">🏫 Vallemar <span>Admin</span></a>
  <a href="<?= SITE_URL ?>/admin/">← Volver</a>
  <?php if ($id): ?>
    <a href="<?= SITE_URL ?>/post.php?slug=<?= e($post['slug'] ?? '') ?>" target="_blank">👁 Ver artículo</a>
  <?php endif; ?>
</nav>
<div class="admin-content">
  <h1 class="page-title"><?= $id ? '✏ Editar artículo' : '+ Nuevo artículo' ?></h1>

  <?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="card">
      <div class="form-group">
        <label>Título *</label>
        <input type="text" name="title" required value="<?= e($post['title'] ?? '') ?>" placeholder="Título del artículo">
      </div>
      <div class="form-group">
        <label>Slug (URL)</label>
        <input type="text" name="slug" value="<?= e($post['slug'] ?? '') ?>" placeholder="url-del-articulo">
        <span class="form-hint">Se genera automáticamente desde el título si se deja vacío.</span>
      </div>
      <div class="form-group">
        <label>Extracto (resumen breve)</label>
        <textarea name="excerpt" rows="3" placeholder="Descripción corta que aparece en el listado…"><?= e($post['excerpt'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Contenido * (HTML permitido)</label>
        <textarea name="body" required placeholder="<p>Escribe el contenido aquí…</p>"><?= e($post['body'] ?? '') ?></textarea>
        <span class="form-hint">Puedes usar etiquetas HTML: &lt;p&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;h2&gt;, etc.</span>
      </div>
    </div>

    <div class="card">
      <div class="form-row">
        <div class="form-group">
          <label>Autor</label>
          <input type="text" name="author" value="<?= e($post['author'] ?? 'Colegio Vallemar') ?>">
        </div>
        <div class="form-group">
          <label>Categoría</label>
          <select name="category_id">
            <option value="">— Sin categoría —</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($post['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Imagen destacada</label>
        <?php if (!empty($post['image'])): ?>
          <img src="<?= SITE_URL ?>/uploads/<?= e($post['image']) ?>" style="max-width:200px;border-radius:8px;margin-bottom:.5rem;display:block">
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">
        <span class="form-hint">JPG, PNG, GIF, WEBP. Máximo recomendado: 2MB.</span>
      </div>
      <div class="form-group">
        <label class="check-group">
          <input type="checkbox" name="published" <?= ($post['published'] ?? 1) ? 'checked' : '' ?>>
          Publicar artículo (visible en el blog)
        </label>
      </div>
    </div>

    <div class="actions">
      <button type="submit" class="btn btn-gold"><?= $id ? '💾 Guardar cambios' : '✓ Crear artículo' ?></button>
      <a href="<?= SITE_URL ?>/admin/" class="back-link">Cancelar</a>
    </div>
  </form>
</div>
</body></html>
