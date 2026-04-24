<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
initDB();

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { redirect(SITE_URL); }

$post = getPost($slug);
if (!$post || !$post['published']) {
    http_response_code(404);
    $pageTitle = 'Artículo no encontrado';
    include __DIR__ . '/includes/header.php';
    echo '<div style="text-align:center;padding:4rem"><h2>404 – Artículo no encontrado</h2><a href="' . SITE_URL . '">← Volver al blog</a></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pdo = getDB();
$msg = ''; $err = '';

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $body  = trim($_POST['body']  ?? '');
    if (!$name || !$email || !$body) {
        $err = 'Por favor completa todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'El correo electrónico no es válido.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, name, email, body) VALUES (?,?,?,?)");
        $stmt->execute([$post['id'], $name, $email, $body]);
        $msg = '¡Gracias! Tu comentario será revisado antes de publicarse.';
    }
}

// Fetch approved comments
$comments = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? AND approved = 1 ORDER BY created_at ASC");
$comments->execute([$post['id']]);
$comments = $comments->fetchAll();

$pageTitle = $post['title'];
include __DIR__ . '/includes/header.php';
?>

<div class="site-wrapper">
  <main>
    <a href="<?= SITE_URL ?>" class="back-link">← Volver al Blog</a>

    <article class="post-full">
      <?php if ($post['image']): ?>
        <img class="post-full-cover" src="<?= SITE_URL ?>/uploads/<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>">
      <?php else: ?>
        <div class="post-full-cover-placeholder">
          <?php
            $icons = ['📚','🏫','✏️','🎨','⚽','🔬','🌍','🎭','🏆','💡'];
            echo $icons[crc32($post['slug']) % count($icons)];
          ?>
        </div>
      <?php endif; ?>

      <div class="post-full-body">
        <?php if ($post['cat_name']): ?>
          <a href="<?= SITE_URL ?>?cat=<?= e($post['cat_slug']) ?>"
             class="post-cat-badge"
             style="background:<?= e($post['cat_color'] ?? '#1a5276') ?>"><?= e($post['cat_name']) ?></a>
        <?php endif; ?>

        <h1><?= e($post['title']) ?></h1>

        <div class="post-meta">
          <span class="post-meta-author">✍ <?= e($post['author']) ?></span>
          <span>📅 <?= date('d/m/Y', strtotime($post['created_at'])) ?></span>
          <span>💬 <?= count($comments) ?> comentario<?= count($comments) !== 1 ? 's' : '' ?></span>
        </div>

        <div class="post-content">
          <?= $post['body'] ?>
        </div>
      </div>
    </article>

    <!-- COMMENTS -->
    <section class="comments-section" style="background:white;border-radius:12px;box-shadow:var(--shadow);padding:2rem;margin-top:2rem">
      <h3>💬 Comentarios (<?= count($comments) ?>)</h3>

      <?php if ($msg): ?>
        <div class="alert alert-success"><?= e($msg) ?></div>
      <?php endif; ?>
      <?php if ($err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
      <?php endif; ?>

      <?php if (empty($comments)): ?>
        <p style="color:var(--muted);font-style:italic;margin-bottom:1.5rem">Sé el primero en comentar este artículo.</p>
      <?php else: ?>
        <?php foreach ($comments as $c): ?>
        <div class="comment-item">
          <span class="comment-author">👤 <?= e($c['name']) ?></span>
          <span class="comment-date"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></span>
          <p class="comment-body"><?= nl2br(e($c['body'])) ?></p>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- Comment form -->
      <div class="comment-form">
        <h3 style="margin-bottom:1rem">Dejar un comentario</h3>
        <form method="post">
          <div class="form-row">
            <div class="form-group">
              <label>Nombre *</label>
              <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>" placeholder="Tu nombre">
            </div>
            <div class="form-group">
              <label>Correo electrónico *</label>
              <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="tu@email.com">
            </div>
          </div>
          <div class="form-group">
            <label>Comentario *</label>
            <textarea name="body" required placeholder="Escribe tu comentario…"><?= e($_POST['body'] ?? '') ?></textarea>
          </div>
          <button type="submit" name="comment_submit" class="btn btn-gold-full">Enviar comentario →</button>
          <p style="font-size:.8rem;color:var(--muted);margin-top:.6rem">Los comentarios son revisados antes de ser publicados.</p>
        </form>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/sidebar.php'; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
