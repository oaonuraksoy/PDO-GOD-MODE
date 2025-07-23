<?php
require_once '../pdo-god-mode-class.php';

/**
 * Temel CRUD İşlemleri Örneği
 * Bu dosya en basit kullanım senaryolarını gösterir
 */

// Veritabanı bağlantısı
$db = new Database([
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'test_db'
]);

echo "<h1>Temel CRUD İşlemleri</h1>";

// CREATE - Veri Ekleme
echo "<h2>1. CREATE - Veri Ekleme</h2>";
$newUser = [
    'name' => 'Ali Veli',
    'email' => 'ali@example.com',
    'age' => 25
];

$userId = $db->insert('users', $newUser);
if ($userId) {
    echo "✅ Yeni kullanıcı eklendi. ID: {$userId}<br>";
} else {
    echo "❌ Hata: " . $db->getError() . "<br>";
}

// READ - Veri Okuma
echo "<h2>2. READ - Veri Okuma</h2>";

// Tüm kullanıcıları getir
$users = $db->select('users');
echo "Toplam kullanıcı: " . count($users) . "<br>";

// Tek kullanıcı getir
$user = $db->selectOne('users', '*', 'id = :id', [':id' => $userId]);
if ($user) {
    echo "Kullanıcı bulundu: {$user['name']} - {$user['email']}<br>";
}

// UPDATE - Veri Güncelleme
echo "<h2>3. UPDATE - Veri Güncelleme</h2>";
$updateData = [
    'name' => 'Ali Veli (Güncellendi)',
    'age' => 26
];

$result = $db->update('users', $updateData, 'id = :id', [':id' => $userId]);
if ($result) {
    echo "✅ Kullanıcı güncellendi<br>";
} else {
    echo "❌ Güncelleme hatası<br>";
}

// DELETE - Veri Silme
echo "<h2>4. DELETE - Veri Silme</h2>";
$result = $db->delete('users', 'id = :id', [':id' => $userId]);
if ($result) {
    echo "✅ Kullanıcı silindi<br>";
} else {
    echo "❌ Silme hatası<br>";
}

echo "<br><strong>Temel CRUD işlemleri tamamlandı!</strong>";
?>