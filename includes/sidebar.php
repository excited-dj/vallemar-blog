<?php
// sidebar.php — call inside site-wrapper
$cats    = getCategories();
$recents = getPosts(5);
?>
<aside class="sidebar">

  <!-- Categories -->
  <div class="sidebar-widget">
    <div class="widget-header">Categorías</div>
    <div class="widget-body">
      <ul class="cat-list">
        <li><a href="<?= SITE_URL ?>"><span>Todos los artículos</span><span class="cat-count"><?= countPosts() ?></span></a></li>
        <?php foreach ($cats as $cat): ?>
        <li>
          <a href="<?= SITE_URL ?>?cat=<?= e($cat['slug']) ?>">
            <span style="display:flex;align-items:center;gap:.4rem">
              <span style="width:10px;height:10px;border-radius:50%;background:<?= e($cat['color']) ?>;flex-shrink:0"></span>
              <?= e($cat['name']) ?>
            </span>
            <span class="cat-count"><?= $cat['post_count'] ?></span>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <!-- Recent posts -->
  <div class="sidebar-widget">
    <div class="widget-header">Artículos Recientes</div>
    <div class="widget-body">
      <?php foreach ($recents as $r): ?>
      <div class="recent-post">
        <div class="recent-thumb">
          <?php if ($r['image']): ?>
            <img src="<?= SITE_URL ?>/uploads/<?= e($r['image']) ?>" alt="">
          <?php else: ?>
            📰
          <?php endif; ?>
        </div>
        <div class="recent-info">
          <a href="<?= SITE_URL ?>/post.php?slug=<?= e($r['slug']) ?>"><?= e($r['title']) ?></a>
          <span><?= timeAgo($r['created_at']) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- About widget -->
  <div class="sidebar-widget">
    <div class="widget-header">Sobre Vallemar</div>
    <div class="widget-body" style="font-size:.9rem;color:var(--muted);line-height:1.6">
      <p>Colegio Internacional Vallemar es un centro educativo ubicado en <strong>La Nucía, Alicante</strong>, con un modelo educativo innovador y visión internacional.</p>
      <a href="https://www.colegiovallemar.com/" target="_blank" style="display:inline-block;margin-top:.75rem;background:var(--navy);color:white;padding:.45rem 1rem;border-radius:8px;text-decoration:none;font-weight:700;font-size:.85rem">Visitar sitio web →</a>
    </div>
  </div>

</aside>
