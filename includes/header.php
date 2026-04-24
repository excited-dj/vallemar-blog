<?php
$lang = $_GET['lang'] ?? 'es';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Blog') ?> · Colegio Vallemar</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
/* твой CSS оставь БЕЗ ИЗМЕНЕНИЙ */
</style>
</head>

<body>

<!-- NAV -->
<nav class="site-nav">
  <div class="nav-inner">

    <a href="<?= SITE_URL ?>" class="nav-logo">
      <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop/d95Kz1868efE3ybZ/logo-cabecera-EvFsMdzAGZvN2aL3.png"
           alt="Vallemar" class="nav-logo-img"
           onerror="this.style.display='none'">

      <div class="nav-logo-text">
        <span>Colegio Vallemar</span>
        <span>Blog Institucional</span>
      </div>
    </a>

    <div class="nav-links">

      <!-- 🌍 LANGUAGE SWITCHER -->
      <a href="<?= SITE_URL ?>?lang=es">ES</a>
      <a href="<?= SITE_URL ?>?lang=ru">RU</a>
      <a href="<?= SITE_URL ?>?lang=en">EN</a>

      <!-- LINKS -->
      <a href="https://www.colegiovallemar.com/" target="_blank">Sitio Web</a>
      <a href="<?= SITE_URL ?>" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
        Blog
      </a>

      <?php if (function_exists('isAdmin') && isAdmin()): ?>
        <a href="<?= SITE_URL ?>/admin/" class="btn-gold">Admin ›</a>
      <?php endif; ?>

    </div>

  </div>
</nav>
