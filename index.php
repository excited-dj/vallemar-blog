<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
initDB(); $lang = $_GET['lang'] ?? 'es';

$perPage = 6;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$search  = trim($_GET['q'] ?? '');
$catSlug = trim($_GET['cat'] ?? '');

// Resolve category
$catId = null; $catObj = null;
if ($catSlug) {
    $cats = getCategories();
    foreach ($cats as $c) {
        if ($c['slug'] === $catSlug) { $catId = (int)$c['id']; $catObj = $c; break; }
    }
}

$posts = getPosts($perPage, $offset, $catId, $search);
$total = countPosts($catId, $search);
$pages = (int)ceil($total / $perPage);

$pageTitle = 'Blog';
if ($catObj) $pageTitle = e($catObj['name']);
if ($search)  $pageTitle = 'Búsqueda: ' . e($search);

include __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<div class="hero-blog">
  <h1>Blog <span>Vallemar</span></h1>
  <p>Noticias, actividades y vida escolar de nuestra comunidad educativa</p>
  <form class="hero-search" method="get" action="<?= SITE_URL ?>">
    <input type="text" name="q" placeholder="Buscar artículos…" value="<?= e($search) ?>">
    <button type="submit">Buscar</button>
  </form>
</div>

<!-- CONTENT -->
<div class="site-wrapper">
  <main>
    <?php if ($catObj): ?>
      <div style="display:flex;align-items:center;gap:.7rem;margin-bottom:1.5rem">
        <span style="background:<?= e($catObj['color']) ?>;color:white;padding:.3rem .9rem;border-radius:50px;font-weight:700;font-size:.85rem"><?= e($catObj['name']) ?></span>
        <span style="color:var(--muted);font-size:.9rem"><?= $total ?> artículo<?= $total !== 1 ? 's' : '' ?></span>
        <a href="<?= SITE_URL ?>" style="margin-left:auto;font-size:.85rem;color:var(--blue);text-decoration:none;font-weight:600">✕ Limpiar filtro</a>
      </div>
    <?php elseif ($search): ?>
      <div style="margin-bottom:1.5rem;display:flex;align-items:center;gap:.7rem">
        <span style="color:var(--muted);font-size:.92rem">Resultados para: <strong style="color:var(--navy)">"<?= e($search) ?>"</strong> — <?= $total ?> encontrado<?= $total !== 1 ? 's' : '' ?></span>
        <a href="<?= SITE_URL ?>" style="margin-left:auto;font-size:.85rem;color:var(--blue);text-decoration:none;font-weight:600">✕ Limpiar</a>
      </div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
      <div style="text-align:center;padding:3rem;background:white;border-radius:12px;box-shadow:var(--shadow)">
        <div style="font-size:3rem;margin-bottom:1rem">📭</div>
        <h2 style="font-family:'Playfair Display',serif;color:var(--navy);margin-bottom:.5rem">No se encontraron artículos</h2>
        <p style="color:var(--muted)">Intenta con otra búsqueda o explora las categorías.</p>
        <a href="<?= SITE_URL ?>" style="display:inline-block;margin-top:1rem;background:var(--blue);color:white;padding:.6rem 1.5rem;border-radius:8px;text-decoration:none;font-weight:700">Ver todos los artículos</a>
      </div>
    <?php else: ?>
      <div class="posts-grid">
        <?php foreach ($posts as $p): ?>
        <article class="post-card">
          <?php if ($p['image']): ?>
            <img class="post-card-img" src="<?= SITE_URL ?>/uploads/<?= e($p['image']) ?>" alt="<?= e($p['title']) ?>">
          <?php else: ?>
            <div class="post-card-placeholder">
              <?php
                $icons = ['📚','🏫','✏️','🎨','⚽','🔬','🌍','🎭','🏆','💡'];
                echo $icons[crc32($p['slug']) % count($icons)];
              ?>
            </div>
          <?php endif; ?>
          <div class="post-card-body">
            <?php if ($p['cat_name']): ?>
              <a href="<?= SITE_URL ?>?cat=<?= e($p['cat_slug']) ?>"
                 class="post-cat-badge"
                 style="background:<?= e($p['cat_color'] ?? '#1a5276') ?>"><?= e($p['cat_name']) ?></a>
            <?php endif; ?>
            <h2><a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h2>
            <p class="post-excerpt"><?= e($p['excerpt'] ?? '') ?></p>
            <div class="post-meta">
              <span class="post-meta-author">✍ <?= e($p['author']) ?></span>
              <span><?= timeAgo($p['created_at']) ?></span>
            </div>
            <a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>" class="read-more">Leer más →</a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <!-- PAGINATION -->
      <?php if ($pages > 1): ?>
      <div class="pagination">
        <?php
        $base = SITE_URL . '?' . http_build_query(array_filter(['q'=>$search,'cat'=>$catSlug]));
        if ($page > 1): ?>
          <a href="<?= $base ?>&page=<?= $page - 1 ?>">‹ Anterior</a>
        <?php endif;
        for ($i = 1; $i <= $pages; $i++):
          if ($i === $page): ?>
            <span class="current"><?= $i ?></span>
          <?php else: ?>
            <a href="<?= $base ?>&page=<?= $i ?>"><?= $i ?></a>
          <?php endif;
        endfor;
        if ($page < $pages): ?>
          <a href="<?= $base ?>&page=<?= $page + 1 ?>">Siguiente ›</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </main>

  <?php include __DIR__ . '/includes/sidebar.php'; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
