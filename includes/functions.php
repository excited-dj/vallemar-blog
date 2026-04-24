<?php
function slug(string $str): string {
    $str = mb_strtolower($str);
    $str = strtr($str, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','ü'=>'u','à'=>'a','è'=>'e','ì'=>'i','ò'=>'o','ù'=>'u']);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', trim($str));
    return $str;
}

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function isAdmin(): bool {
    return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
}

function requireAdmin(): void {
    if (!isAdmin()) {
        redirect(SITE_URL . '/admin/login.php');
    }
}

function timeAgo(string $datetime): string {
    $now  = new DateTime();
    $then = new DateTime($datetime);
    $diff = $now->diff($then);
    if ($diff->y > 0) return $diff->y . ' año' . ($diff->y > 1 ? 's' : '') . ' atrás';
    if ($diff->m > 0) return $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '') . ' atrás';
    if ($diff->d > 0) return $diff->d . ' día' . ($diff->d > 1 ? 's' : '') . ' atrás';
    if ($diff->h > 0) return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
    if ($diff->i > 0) return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
    return 'Ahora mismo';
}

function getPosts(int $limit = 10, int $offset = 0, ?int $catId = null, string $search = ''): array {
    $pdo  = getDB();
    $where = ['p.published = 1'];
    $params = [];
    if ($catId) {
        $where[] = 'p.category_id = :cat';
        $params[':cat'] = $catId;
    }
    if ($search) {
        $where[] = '(p.title LIKE :s OR p.excerpt LIKE :s2)';
        $params[':s']  = "%$search%";
        $params[':s2'] = "%$search%";
    }
    $sql = "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, c.color AS cat_color
            FROM posts p LEFT JOIN categories c ON p.category_id = c.id
            WHERE " . implode(' AND ', $where) .
           " ORDER BY p.created_at DESC LIMIT :lim OFFSET :off";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function countPosts(?int $catId = null, string $search = ''): int {
    $pdo = getDB();
    $where = ['p.published = 1'];
    $params = [];
    if ($catId) { $where[] = 'p.category_id = :cat'; $params[':cat'] = $catId; }
    if ($search) { $where[] = '(p.title LIKE :s OR p.excerpt LIKE :s2)'; $params[':s'] = "%$search%"; $params[':s2'] = "%$search%"; }
    $sql = "SELECT COUNT(*) FROM posts p WHERE " . implode(' AND ', $where);
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->execute();
    return (int)$stmt->fetchColumn();
}

function getPost(string $slugOrId, bool $byId = false): ?array {
    $pdo = getDB();
    $field = $byId ? 'p.id' : 'p.slug';
    $stmt = $pdo->prepare("SELECT p.*, c.name AS cat_name, c.slug AS cat_slug, c.color AS cat_color
        FROM posts p LEFT JOIN categories c ON p.category_id = c.id
        WHERE $field = ? LIMIT 1");
    $stmt->execute([$byId ? (int)$slugOrId : $slugOrId]);
    $r = $stmt->fetch();
    return $r ?: null;
}

function getCategories(): array {
    return getDB()->query("SELECT *, (SELECT COUNT(*) FROM posts WHERE category_id = categories.id AND published=1) AS post_count FROM categories ORDER BY name")->fetchAll();
}
?>
