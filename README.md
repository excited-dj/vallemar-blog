# 🏫 Blog Colegio Vallemar — PHP

Blog institucional completo para Colegio Internacional Vallemar (La Nucía, Alicante).

## Requisitos
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Servidor web (Apache / Nginx) con mod_rewrite

## Instalación

### 1. Copia los archivos
Sube toda la carpeta `vallemar-blog/` a tu servidor web (ej: `/var/www/html/vallemar-blog/` o `public_html/blog/`).

### 2. Crea la base de datos
```sql
CREATE DATABASE vallemar_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Configura la conexión
Edita `includes/db.php` y ajusta:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'vallemar_blog');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('SITE_URL', 'https://tudominio.com/blog');  // ← importante
```

### 4. Cambia la contraseña del admin
En `includes/db.php`, reemplaza `ADMIN_PASS` con un nuevo hash:
```php
// Genera el hash en PHP:
echo password_hash('tu_nueva_contraseña', PASSWORD_DEFAULT);
```

### 5. Permisos de uploads
```bash
chmod 755 uploads/
```

### 6. Primera visita
Abre `https://tudominio.com/blog/` — las tablas se crean automáticamente con datos de ejemplo.

## Acceso al Panel de Administración
URL: `https://tudominio.com/blog/admin/login.php`
- Usuario: `admin`
- Contraseña: `password` *(cámbiala en producción)*

## Estructura de archivos
```
vallemar-blog/
├── index.php              ← Listado del blog
├── post.php               ← Artículo individual + comentarios
├── setup.sql              ← SQL de referencia
├── uploads/               ← Imágenes subidas
├── includes/
│   ├── db.php             ← Configuración y DB init
│   ├── functions.php      ← Funciones helper
│   ├── header.php         ← Cabecera HTML
│   ├── sidebar.php        ← Barra lateral
│   └── footer.php         ← Pie de página
└── admin/
    ├── index.php          ← Dashboard con stats
    ├── edit-post.php      ← Crear / editar artículos
    ├── comments.php       ← Moderación de comentarios
    ├── categories.php     ← Gestión de categorías
    ├── login.php          ← Login admin
    └── logout.php
```

## Funcionalidades
- ✅ Listado de artículos con paginación
- ✅ Búsqueda de artículos
- ✅ Filtro por categorías
- ✅ Artículo individual con comentarios
- ✅ Sistema de comentarios con moderación
- ✅ Panel de administración completo
- ✅ Crear / editar / borrar artículos
- ✅ Subida de imágenes destacadas
- ✅ Gestión de categorías con colores
- ✅ Estadísticas en el dashboard
- ✅ Diseño responsivo adaptado a Colegio Vallemar
