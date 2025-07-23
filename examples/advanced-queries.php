<?php
require_once '../pdo-god-mode-class.php';

/**
 * Gelişmiş Sorgu Örnekleri
 * Karmaşık veritabanı işlemleri ve optimizasyon teknikleri
 */

$db = new Database([
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'test_db'
]);

echo "<h1>Gelişmiş Sorgu Örnekleri</h1>";

// =============================================================================
// 1. KARMAŞIK WHERE KOŞULLARI
// =============================================================================
echo "<h2>1. Karmaşık WHERE Koşulları</h2>";

// Çoklu koşul
$users = $db->select(
    'users', 
    '*', 
    'age BETWEEN :min_age AND :max_age AND status = :status',
    [':min_age' => 18, ':max_age' => 65, ':status' => 'active']
);
echo "18-65 yaş arası aktif kullanıcılar: " . count($users) . "<br>";

// LIKE sorgusu
$users = $db->select(
    'users',
    '*',
    'name LIKE :search OR email LIKE :search',
    [':search' => '%ahmet%']
);
echo "Adında veya emailinde 'ahmet' geçen kullanıcılar: " . count($users) . "<br>";

// IN sorgusu
$users = $db->select(
    'users',
    '*',
    'id IN (1, 2, 3, 4, 5)'
);
echo "ID'si 1-5 arasında olan kullanıcılar: " . count($users) . "<br>";

// =============================================================================
// 2. JOIN İŞLEMLERİ
// =============================================================================
echo "<h2>2. JOIN İşlemleri</h2>";

// INNER JOIN
$sql = "
    SELECT u.name, u.email, p.title, p.created_at
    FROM users u
    INNER JOIN posts p ON u.id = p.user_id
    WHERE p.status = :status
    ORDER BY p.created_at DESC
";
$userPosts = $db->query($sql, [':status' => 'published']);
echo "Yayınlanmış yazıları olan kullanıcılar: " . count($userPosts) . "<br>";

// LEFT JOIN
$sql = "
    SELECT u.name, u.email, COUNT(p.id) as post_count
    FROM users u
    LEFT JOIN posts p ON u.id = p.user_id
    GROUP BY u.id, u.name, u.email
    HAVING post_count > :min_posts
";
$activeWriters = $db->query($sql, [':min_posts' => 5]);
echo "5'ten fazla yazısı olan yazarlar: " . count($activeWriters) . "<br>";

// =============================================================================
// 3. AGGREGATE FONKSİYONLAR
// =============================================================================
echo "<h2>3. Aggregate Fonksiyonlar</h2>";

// COUNT, AVG, MIN, MAX
$sql = "
    SELECT 
        COUNT(*) as total_users,
        AVG(age) as avg_age,
        MIN(age) as min_age,
        MAX(age) as max_age
    FROM users 
    WHERE status = :status
";
$stats = $db->query($sql, [':status' => 'active']);
if ($stats) {
    $stat = $stats[0];
    echo "İstatistikler:<br>";
    echo "- Toplam kullanıcı: {$stat['total_users']}<br>";
    echo "- Ortalama yaş: " . round($stat['avg_age'], 1) . "<br>";
    echo "- En küçük yaş: {$stat['min_age']}<br>";
    echo "- En büyük yaş: {$stat['max_age']}<br>";
}

// GROUP BY
$sql = "
    SELECT status, COUNT(*) as count
    FROM users
    GROUP BY status
    ORDER BY count DESC
";
$statusCounts = $db->query($sql);
echo "Duruma göre kullanıcı sayıları:<br>";
foreach ($statusCounts as $row) {
    echo "- {$row['status']}: {$row['count']}<br>";
}

// =============================================================================
// 4. SUBQUERY KULLANIMI
// =============================================================================
echo "<h2>4. Subquery Kullanımı</h2>";

// EXISTS subquery
$sql = "
    SELECT u.name, u.email
    FROM users u
    WHERE EXISTS (
        SELECT 1 FROM posts p 
        WHERE p.user_id = u.id 
        AND p.created_at > :date
    )
";
$recentWriters = $db->query($sql, [':date' => date('Y-m-d', strtotime('-30 days'))]);
echo "Son 30 günde yazı yazan kullanıcılar: " . count($recentWriters) . "<br>";

// IN subquery
$sql = "
    SELECT *
    FROM posts
    WHERE user_id IN (
        SELECT id FROM users WHERE status = :status
    )
    ORDER BY created_at DESC
";
$activePosts = $db->query($sql, [':status' => 'active']);
echo "Aktif kullanıcıların yazıları: " . count($activePosts) . "<br>";

// =============================================================================
// 5. PAGINATION
// =============================================================================
echo "<h2>5. Pagination (Sayfalama)</h2>";

function getPaginatedUsers($db, $page = 1, $perPage = 10) {
    $offset = ($page - 1) * $perPage;
    
    // Toplam kayıt sayısı
    $totalCount = $db->count('users');
    
    // Sayfalı veriler
    $users = $db->select(
        'users',
        '*',
        '',
        [],
        'created_at DESC',
        "{$offset}, {$perPage}"
    );
    
    return [
        'data' => $users,
        'total' => $totalCount,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => ceil($totalCount / $perPage)
    ];
}

$pagination = getPaginatedUsers($db, 1, 5);
echo "Sayfa 1/5 kullanıcılar:<br>";
echo "Toplam: {$pagination['total']}, Sayfa: {$pagination['page']}/{$pagination['total_pages']}<br>";

// =============================================================================
// 6. FULL-TEXT SEARCH
// =============================================================================
echo "<h2>6. Full-Text Search</h2>";

// MATCH AGAINST (MySQL Full-Text Search)
$sql = "
    SELECT *, MATCH(title, content) AGAINST(:search) as relevance
    FROM posts
    WHERE MATCH(title, content) AGAINST(:search)
    ORDER BY relevance DESC
";
$searchResults = $db->query($sql, [':search' => 'php mysql']);
echo "Full-text arama sonuçları: " . count($searchResults) . "<br>";

// =============================================================================
// 7. UNION SORGUSU
// =============================================================================
echo "<h2>7. UNION Sorgusu</h2>";

$sql = "
    (SELECT 'user' as type, name, email, created_at FROM users WHERE status = 'active')
    UNION
    (SELECT 'admin' as type, name, email, created_at FROM admins WHERE status = 'active')
    ORDER BY created_at DESC
    LIMIT 10
";
$allActiveUsers = $db->query($sql);
echo "Tüm aktif kullanıcılar (user + admin): " . count($allActiveUsers) . "<br>";

// =============================================================================
// 8. WINDOW FUNCTIONS (MySQL 8.0+)
// =============================================================================
echo "<h2>8. Window Functions</h2>";

$sql = "
    SELECT 
        name,
        age,
        ROW_NUMBER() OVER (ORDER BY age DESC) as age_rank,
        RANK() OVER (ORDER BY age DESC) as age_rank_with_ties
    FROM users
    WHERE status = 'active'
    ORDER BY age DESC
";
$rankedUsers = $db->query($sql);
echo "Yaşa göre sıralanmış kullanıcılar: " . count($rankedUsers) . "<br>";

// =============================================================================
// 9. PERFORMANS OPTİMİZASYONU
// =============================================================================
echo "<h2>9. Performans Optimizasyonu</h2>";

// Index kullanımını kontrol et
$sql = "EXPLAIN SELECT * FROM users WHERE email = :email";
$explain = $db->query($sql, [':email' => 'test@example.com']);

// Slow query log analizi için
$startTime = microtime(true);
$users = $db->select('users', '*', 'status = :status', [':status' => 'active']);
$endTime = microtime(true);
$queryTime = ($endTime - $startTime) * 1000;
echo "Sorgu süresi: " . round($queryTime, 2) . " ms<br>";

// =============================================================================
// 10. BATCH İŞLEMLER
// =============================================================================
echo "<h2>10. Batch İşlemler</h2>";

// Toplu insert
$batchData = [];
for ($i = 1; $i <= 100; $i++) {
    $batchData[] = [
        'name' => "Test User {$i}",
        'email' => "test{$i}@example.com",
        'age' => rand(18, 65),
        'created_at' => date('Y-m-d H:i:s')
    ];
}

$db->beginTransaction();
try {
    $insertCount = 0;
    foreach ($batchData as $data) {
        if ($db->insert('users', $data)) {
            $insertCount++;
        }
    }
    $db->commit();
    echo "✅ Toplu insert başarılı: {$insertCount} kayıt eklendi<br>";
} catch (Exception $e) {
    $db->rollback();
    echo "❌ Toplu insert hatası: " . $e->getMessage() . "<br>";
}

echo "<br><strong>Gelişmiş sorgu örnekleri tamamlandı!</strong>";
?>