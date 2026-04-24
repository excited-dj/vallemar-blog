<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (isAdmin()) redirect(SITE_URL . '/admin/');

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS)) {
        $_SESSION['admin'] = true;
        redirect(SITE_URL . '/admin/');
    } else {
        $err = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin · Colegio Vallemar Blog</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--navy:#0d2b4e;--blue:#1a5276;--gold:#d4a82a;--light:#f0f4f8}
body{font-family:'Nunito',sans-serif;background:linear-gradient(135deg,var(--navy),var(--blue));min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
.login-box{background:white;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.3);padding:2.5rem;width:100%;max-width:400px}
.login-logo{text-align:center;margin-bottom:2rem}
.login-logo h1{font-family:'Playfair Display',serif;color:var(--navy);font-size:1.6rem}
.login-logo p{color:#6b7c8d;font-size:.9rem;margin-top:.3rem}
.login-logo::after{content:'';display:block;width:50px;height:3px;background:var(--gold);margin:.8rem auto 0;border-radius:2px}
.form-group{display:flex;flex-direction:column;gap:.3rem;margin-bottom:1rem}
.form-group label{font-weight:700;font-size:.85rem;color:var(--navy)}
.form-group input{border:1.5px solid #c8d6e3;border-radius:8px;padding:.7rem 1rem;font-family:'Nunito',sans-serif;font-size:.95rem;outline:none;transition:border .2s}
.form-group input:focus{border-color:var(--blue)}
.btn{width:100%;background:var(--navy);color:white;border:none;border-radius:8px;padding:.8rem;font-family:'Nunito',sans-serif;font-weight:700;font-size:1rem;cursor:pointer;transition:background .2s;margin-top:.5rem}
.btn:hover{background:var(--blue)}
.alert{padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem;font-weight:600;font-size:.88rem;background:#fadbd8;color:#c0392b}
.back{display:block;text-align:center;margin-top:1rem;color:var(--blue);text-decoration:none;font-size:.88rem;font-weight:600}
.hint{font-size:.78rem;color:#6b7c8d;text-align:center;margin-top:.75rem}
</style>
</head>
<body>
<div class="login-box">
  <div class="login-logo">
    <h1>🏫 Panel Admin</h1>
    <p>Colegio Vallemar · Blog</p>
  </div>
  <?php if ($err): ?><div class="alert"><?= e($err) ?></div><?php endif; ?>
  <form method="post">
    <div class="form-group">
      <label>Usuario</label>
      <input type="text" name="username" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Contraseña</label>
      <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn">Entrar →</button>
  </form>
  <p class="hint">Usuario: <code>admin</code> · Contraseña: <code>password</code></p>
  <a href="<?= SITE_URL ?>" class="back">← Volver al blog</a>
</div>
</body>
</html>
