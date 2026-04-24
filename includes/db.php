<?php
// ── Database Configuration ──────────────────────────────────────────────────
// Change these values to match your server
define('DB_HOST', 'localhost');
define('DB_NAME', 'vallemar_blog');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site config
define('SITE_NAME', 'Colegio Vallemar · Blog');
define('SITE_URL', 'http://localhost/vallemar-blog');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); // password: password

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

function initDB(): void {
    $pdo = getDB();
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id       INT AUTO_INCREMENT PRIMARY KEY,
            name     VARCHAR(100) NOT NULL,
            slug     VARCHAR(100) NOT NULL UNIQUE,
            color    VARCHAR(7) DEFAULT '#1a5276'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS posts (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            category_id  INT DEFAULT NULL,
            title        VARCHAR(255) NOT NULL,
            slug         VARCHAR(255) NOT NULL UNIQUE,
            excerpt      TEXT,
            body         LONGTEXT NOT NULL,
            image        VARCHAR(255) DEFAULT NULL,
            author       VARCHAR(100) DEFAULT 'Colegio Vallemar',
            published    TINYINT(1) DEFAULT 1,
            created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS comments (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            post_id    INT NOT NULL,
            name       VARCHAR(100) NOT NULL,
            email      VARCHAR(150) NOT NULL,
            body       TEXT NOT NULL,
            approved   TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Seed default categories
    $count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO categories (name, slug, color) VALUES
            ('Noticias',         'noticias',         '#1a5276'),
            ('Vida Escolar',     'vida-escolar',     '#117a65'),
            ('Actividades',      'actividades',      '#784212'),
            ('Logros Académicos','logros-academicos','#6c3483'),
            ('Familias',         'familias',         '#1f618d');

            INSERT INTO posts (category_id, title, slug, excerpt, body, author) VALUES
            (1, 'Bienvenidos al Blog de Colegio Vallemar',
                'bienvenidos-blog-vallemar',
                'Con gran entusiasmo lanzamos el blog oficial de Colegio Internacional Vallemar, un espacio donde compartiremos noticias, experiencias y logros de nuestra comunidad educativa.',
                '<p>Con gran entusiasmo lanzamos el <strong>blog oficial de Colegio Internacional Vallemar</strong>, un espacio vivo y dinámico donde la comunidad educativa podrá seguir de cerca todo lo que ocurre dentro y fuera de nuestras aulas.</p><p>Aquí encontrarás noticias sobre eventos escolares, reportajes sobre proyectos innovadores de nuestros alumnos, entrevistas con docentes y mucho más. Nuestro objetivo es que este blog sea un puente de comunicación entre el colegio y las familias.</p><p>¡Os damos la bienvenida y esperamos que disfrutéis de esta nueva aventura digital con nosotros!</p>',
                'Equipo Vallemar'),
            (2, 'Jornada de Puertas Abiertas 2026 – 28 de Marzo',
                'puertas-abiertas-2026',
                'El próximo sábado 28 de marzo celebramos nuestra primera Jornada de Puertas Abiertas. Descubre cómo trabajamos y conoce nuestras instalaciones.',
                '<p>El próximo <strong>sábado 28 de marzo a las 09:30 h</strong> celebraremos en Vallemar International School nuestra primera Jornada de <strong>Puertas Abiertas</strong>, un encuentro especialmente pensado para las familias que desean conocer de cerca nuestro proyecto educativo.</p><p>Durante la mañana se podrán:</p><ul><li>Recorrer las instalaciones del centro.</li><li>Visitar las aulas en funcionamiento.</li><li>Descubrir cómo trabajan nuestros alumnos a través de proyectos reales.</li><li>Hablar directamente con el equipo docente y directivo.</li></ul><p><strong>Para asistir es necesaria inscripción previa</strong> a través de nuestra página web. ¡Les esperamos!</p>',
                'Dirección Vallemar'),
            (3, 'Programa Talent in Motion – Nuevas Incorporaciones',
                'talent-in-motion-2026',
                'Nuestro programa estrella Talent in Motion amplía su oferta este curso con nuevas disciplinas artísticas, deportivas y tecnológicas para todos los niveles.',
                '<p>El <strong>Programa Talent in Motion</strong> es uno de los pilares de la propuesta educativa de Vallemar. Este curso incorporamos nuevas actividades que responden a los intereses y talentos de nuestros alumnos.</p><p>Entre las novedades destacamos:</p><ul><li><strong>Robótica avanzada</strong> para Secundaria y Bachillerato.</li><li><strong>Teatro en inglés</strong> para Educación Primaria.</li><li><strong>Emprendimiento y finanzas personales</strong> para Bachillerato.</li><li><strong>Arte digital y diseño gráfico</strong> para todos los niveles.</li></ul><p>Creemos firmemente que cada alumno tiene un talento único que merece ser cultivado. Talent in Motion es el espacio donde eso ocurre.</p>',
                'Coordinación Extracurricular');
        ");
    }
}
?>
