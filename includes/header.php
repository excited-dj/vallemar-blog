<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Blog') ?> · Colegio Vallemar</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --navy:   #0d2b4e;
  --blue:   #1a5276;
  --sky:    #2e86c1;
  --gold:   #d4a82a;
  --light:  #f0f4f8;
  --white:  #ffffff;
  --text:   #1c2b3a;
  --muted:  #6b7c8d;
  --radius: 12px;
  --shadow: 0 4px 24px rgba(13,43,78,.10);
}

body {
  font-family: 'Nunito', sans-serif;
  background: var(--light);
  color: var(--text);
  line-height: 1.7;
  min-height: 100vh;
}

/* ── NAV ─────────────────────────────────────────────── */
.site-nav {
  background: var(--navy);
  position: sticky; top: 0; z-index: 100;
  box-shadow: 0 2px 12px rgba(0,0,0,.25);
}
.nav-inner {
  max-width: 1180px; margin: 0 auto;
  display: flex; align-items: center; gap: 1.5rem;
  padding: .75rem 1.5rem;
}
.nav-logo { display: flex; align-items: center; gap: .75rem; text-decoration: none; }
.nav-logo-img { height: 44px; }
.nav-logo-text { display: flex; flex-direction: column; }
.nav-logo-text span:first-child {
  font-family: 'Playfair Display', serif;
  color: var(--white); font-size: 1.1rem; font-weight: 700; line-height: 1.1;
}
.nav-logo-text span:last-child {
  color: var(--gold); font-size: .72rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
}
.nav-links { display: flex; gap: .25rem; margin-left: auto; align-items: center; }
.nav-links a {
  color: rgba(255,255,255,.8); text-decoration: none;
  padding: .45rem .9rem; border-radius: 6px;
  font-weight: 600; font-size: .9rem; transition: all .2s;
}
.nav-links a:hover, .nav-links a.active {
  background: rgba(255,255,255,.12); color: var(--white);
}
.nav-links .btn-gold {
  background: var(--gold); color: var(--navy) !important;
  padding: .45rem 1rem; border-radius: 6px;
}
.nav-links .btn-gold:hover { background: #e8b830; }

/* ── HERO BANNER ─────────────────────────────────────── */
.hero-blog {
  background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 60%, var(--sky) 100%);
  padding: 3.5rem 1.5rem 4rem;
  text-align: center;
  position: relative; overflow: hidden;
}
.hero-blog::before {
  content: '';
  position: absolute; inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  pointer-events: none;
}
.hero-blog h1 {
  font-family: 'Playfair Display', serif;
  color: var(--white); font-size: clamp(2rem, 5vw, 3rem);
  margin-bottom: .5rem; position: relative;
}
.hero-blog h1 span { color: var(--gold); }
.hero-blog p { color: rgba(255,255,255,.75); font-size: 1.05rem; max-width: 500px; margin: 0 auto 1.5rem; }
.hero-search {
  display: flex; max-width: 460px; margin: 0 auto;
  background: rgba(255,255,255,.15); border: 1.5px solid rgba(255,255,255,.3);
  border-radius: 50px; overflow: hidden; backdrop-filter: blur(4px);
}
.hero-search input {
  flex: 1; background: transparent; border: none; outline: none;
  color: var(--white); padding: .7rem 1.2rem; font-family: 'Nunito', sans-serif; font-size: .95rem;
}
.hero-search input::placeholder { color: rgba(255,255,255,.55); }
.hero-search button {
  background: var(--gold); color: var(--navy);
  border: none; padding: .7rem 1.3rem; cursor: pointer;
  font-weight: 700; font-size: .9rem; transition: background .2s;
}
.hero-search button:hover { background: #e8b830; }

/* ── LAYOUT ──────────────────────────────────────────── */
.site-wrapper {
  max-width: 1180px; margin: 0 auto;
  padding: 2.5rem 1.5rem 4rem;
  display: grid; grid-template-columns: 1fr 300px; gap: 2rem;
}
@media (max-width: 860px) {
  .site-wrapper { grid-template-columns: 1fr; }
}

/* ── CARDS ───────────────────────────────────────────── */
.posts-grid { display: flex; flex-direction: column; gap: 2rem; }
.post-card {
  background: var(--white); border-radius: var(--radius);
  box-shadow: var(--shadow); overflow: hidden;
  display: grid; grid-template-columns: 280px 1fr;
  transition: transform .25s, box-shadow .25s;
}
.post-card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(13,43,78,.16); }
@media (max-width: 640px) { .post-card { grid-template-columns: 1fr; } }
.post-card-img {
  width: 100%; height: 200px; object-fit: cover;
  display: block; background: var(--light);
}
.post-card-placeholder {
  width: 100%; height: 200px;
  background: linear-gradient(135deg, var(--navy), var(--sky));
  display: flex; align-items: center; justify-content: center;
  font-size: 3rem;
}
.post-card-body { padding: 1.5rem; display: flex; flex-direction: column; }
.post-cat-badge {
  display: inline-block; font-size: .72rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: .07em;
  padding: .25rem .7rem; border-radius: 50px;
  color: var(--white); margin-bottom: .6rem;
  text-decoration: none;
}
.post-card-body h2 { font-family: 'Playfair Display', serif; font-size: 1.25rem; line-height: 1.3; margin-bottom: .6rem; }
.post-card-body h2 a { color: var(--navy); text-decoration: none; }
.post-card-body h2 a:hover { color: var(--sky); }
.post-excerpt { color: var(--muted); font-size: .93rem; flex: 1; }
.post-meta {
  display: flex; align-items: center; gap: 1rem;
  margin-top: 1rem; padding-top: .75rem;
  border-top: 1px solid var(--light); font-size: .82rem; color: var(--muted);
}
.post-meta-author { font-weight: 600; color: var(--blue); }
.read-more {
  display: inline-flex; align-items: center; gap: .3rem;
  color: var(--sky); font-weight: 700; font-size: .88rem;
  text-decoration: none; margin-top: .5rem;
  transition: gap .2s;
}
.read-more:hover { gap: .6rem; }

/* ── PAGINATION ──────────────────────────────────────── */
.pagination { display: flex; gap: .5rem; justify-content: center; margin-top: 2rem; }
.pagination a, .pagination span {
  padding: .5rem .9rem; border-radius: 8px;
  font-weight: 700; font-size: .9rem; text-decoration: none;
  border: 1.5px solid var(--blue); color: var(--blue);
  transition: all .2s;
}
.pagination a:hover, .pagination .current {
  background: var(--blue); color: var(--white);
}

/* ── SIDEBAR ─────────────────────────────────────────── */
.sidebar { display: flex; flex-direction: column; gap: 1.5rem; }
.sidebar-widget {
  background: var(--white); border-radius: var(--radius);
  box-shadow: var(--shadow); overflow: hidden;
}
.widget-header {
  background: var(--navy); color: var(--white);
  padding: .8rem 1.2rem;
  font-family: 'Playfair Display', serif; font-size: 1rem;
  display: flex; align-items: center; gap: .5rem;
}
.widget-header::before { content: ''; display: block; width: 4px; height: 1em; background: var(--gold); border-radius: 2px; }
.widget-body { padding: 1.2rem; }
.cat-list { list-style: none; display: flex; flex-direction: column; gap: .5rem; }
.cat-list li a {
  display: flex; justify-content: space-between; align-items: center;
  text-decoration: none; color: var(--text);
  padding: .45rem .7rem; border-radius: 8px;
  font-weight: 600; font-size: .92rem; transition: all .2s;
}
.cat-list li a:hover { background: var(--light); color: var(--blue); }
.cat-count {
  background: var(--light); border-radius: 50px;
  font-size: .75rem; font-weight: 700; color: var(--muted);
  padding: .15rem .55rem;
}
.recent-post { display: flex; gap: .75rem; padding: .5rem 0; border-bottom: 1px solid var(--light); }
.recent-post:last-child { border-bottom: none; }
.recent-thumb {
  width: 56px; height: 56px; border-radius: 8px;
  background: linear-gradient(135deg, var(--navy), var(--sky));
  flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
  overflow: hidden;
}
.recent-thumb img { width: 100%; height: 100%; object-fit: cover; }
.recent-info a { display: block; font-weight: 600; font-size: .88rem; color: var(--navy); text-decoration: none; line-height: 1.3; }
.recent-info a:hover { color: var(--sky); }
.recent-info span { font-size: .78rem; color: var(--muted); }

/* ── FOOTER ──────────────────────────────────────────── */
.site-footer {
  background: var(--navy); color: rgba(255,255,255,.65);
  text-align: center; padding: 2rem 1.5rem;
  font-size: .88rem;
}
.site-footer a { color: var(--gold); text-decoration: none; }
.site-footer a:hover { text-decoration: underline; }

/* ── SINGLE POST ─────────────────────────────────────── */
.post-full {
  background: var(--white); border-radius: var(--radius);
  box-shadow: var(--shadow); overflow: hidden;
}
.post-full-cover { width: 100%; max-height: 380px; object-fit: cover; display: block; }
.post-full-cover-placeholder {
  height: 280px;
  background: linear-gradient(135deg, var(--navy), var(--sky));
  display: flex; align-items: center; justify-content: center;
  font-size: 5rem;
}
.post-full-body { padding: 2rem; }
.post-full-body h1 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(1.5rem, 4vw, 2.2rem); color: var(--navy);
  line-height: 1.25; margin-bottom: .8rem;
}
.post-full-body .post-meta { margin-bottom: 1.5rem; }
.post-content { font-size: 1.02rem; line-height: 1.85; }
.post-content p { margin-bottom: 1rem; }
.post-content h2, .post-content h3 { font-family: 'Playfair Display', serif; color: var(--navy); margin: 1.5rem 0 .5rem; }
.post-content ul, .post-content ol { margin: .75rem 0 .75rem 1.5rem; }
.post-content li { margin-bottom: .3rem; }
.post-content strong { color: var(--navy); }
.back-link {
  display: inline-flex; align-items: center; gap: .4rem;
  color: var(--blue); font-weight: 700; text-decoration: none;
  margin-bottom: 1.5rem; font-size: .92rem;
}
.back-link:hover { color: var(--navy); }

/* ── COMMENTS ────────────────────────────────────────── */
.comments-section { margin-top: 2.5rem; }
.comments-section h3 {
  font-family: 'Playfair Display', serif; color: var(--navy);
  font-size: 1.4rem; margin-bottom: 1.2rem;
  padding-bottom: .5rem; border-bottom: 2px solid var(--gold);
}
.comment-item {
  background: var(--light); border-radius: 10px;
  padding: 1rem 1.2rem; margin-bottom: 1rem;
}
.comment-author { font-weight: 700; color: var(--navy); font-size: .92rem; }
.comment-date { font-size: .78rem; color: var(--muted); margin-left: .5rem; }
.comment-body { margin-top: .4rem; font-size: .94rem; }
.comment-form { margin-top: 2rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 560px) { .form-row { grid-template-columns: 1fr; } }
.form-group { display: flex; flex-direction: column; gap: .35rem; margin-bottom: .9rem; }
.form-group label { font-weight: 600; font-size: .88rem; color: var(--navy); }
.form-group input, .form-group textarea {
  border: 1.5px solid #c8d6e3; border-radius: 8px;
  padding: .65rem .9rem; font-family: 'Nunito', sans-serif;
  font-size: .95rem; outline: none; transition: border .2s;
}
.form-group input:focus, .form-group textarea:focus { border-color: var(--sky); }
.form-group textarea { resize: vertical; min-height: 100px; }
.btn {
  background: var(--blue); color: var(--white);
  border: none; border-radius: 8px; padding: .7rem 1.8rem;
  font-family: 'Nunito', sans-serif; font-weight: 700;
  font-size: .95rem; cursor: pointer; transition: background .2s;
}
.btn:hover { background: var(--navy); }
.btn-gold-full { background: var(--gold); color: var(--navy); }
.btn-gold-full:hover { background: #e8b830; }

/* ── ALERTS ──────────────────────────────────────────── */
.alert {
  padding: .8rem 1.2rem; border-radius: 8px; margin-bottom: 1rem;
  font-weight: 600; font-size: .92rem;
}
.alert-success { background: #d4efdf; color: #1e8449; }
.alert-error   { background: #fadbd8; color: #c0392b; }
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
      <a href="https://www.colegiovallemar.com/" target="_blank">Sitio Web</a>
      <a href="<?= SITE_URL ?>" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Blog</a>
      <?php if (isAdmin()): ?>
        <a href="<?= SITE_URL ?>/admin/" class="btn-gold">Admin ›</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
