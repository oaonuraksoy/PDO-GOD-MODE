# PDO God Mode Database Class 🚀

**[🇺🇸 Click here to read in English / İngilizce okumak için buraya tıklayın](README.en.md)**

**Güçlü, Güvenli ve Esnek PHP PDO Veritabanı Sınıfı**

PDO God Mode, PHP projelerinizde veritabanı işlemlerini kolaylaştıran, güvenli ve performanslı bir PDO wrapper sınıfıdır. Herhangi bir tablo yapısıyla çalışabilir ve gelişmiş özellikler sunar.

## ✨ Özellikler

- 🔒 **Güvenli**: SQL Injection koruması ile prepared statements
- 🚀 **Performanslı**: Optimized queries ve connection pooling
- 🔧 **Esnek**: Herhangi bir tablo yapısıyla çalışır
- 📁 **Dosya Yükleme**: Gelişmiş dosya yükleme sistemi
- 🔄 **Transaction Desteği**: ACID uyumlu işlemler
- 📊 **Gelişmiş Sorgular**: JOIN, subquery, aggregate functions
- 🛠️ **Hata Yönetimi**: Kapsamlı hata yakalama ve raporlama
- 📝 **Logging**: Query logging ve debugging
- 🎯 **Kolay Kullanım**: Minimal kod ile maksimum işlevsellik

## 📋 Gereksinimler

- PHP 7.4 veya üzeri
- PDO extension
- MySQL 5.7+ veya MariaDB 10.2+

## 🚀 Kurulum

1. Dosyaları projenize kopyalayın:
```bash
git clone https://github.com/username/pdo-god-mode.git
```

2. Sınıfı projenizde kullanın:
```php
require_once 'pdo-god-mode-class.php';
$db = new Database($config);
```

## 📖 Temel Kullanım

### Veritabanı Bağlantısı

```php
// Varsayılan ayarlarla
$db = new Database();

// Özel ayarlarla
$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'test_db',
    'charset' => 'utf8mb4'
];
$db = new Database($config);
```

### CREATE - Veri Ekleme

```php
// Basit insert
$userData = [
    'name' => 'Ahmet Yılmaz',
    'email' => 'ahmet@example.com',
    'phone' => '0555-123-4567'
];

$userId = $db->insert('users', $userData);
if ($userId) {
    echo "Kullanıcı eklendi. ID: {$userId}";
}
```

### READ - Veri Okuma

```php
// Tüm kayıtları getir
$users = $db->select('users');

// Koşullu sorgular
$activeUsers = $db->select('users', '*', 'status = :status', [':status' => 'active']);

// Tek kayıt getir
$user = $db->selectOne('users', '*', 'id = :id', [':id' => 1]);

// Sayım
$userCount = $db->count('users', 'status = :status', [':status' => 'active']);

// Sıralama ve limit
$recentUsers = $db->select('users', '*', '', [], 'created_at DESC', '10');
```

### UPDATE - Veri Güncelleme

```php
$updateData = [
    'name' => 'Ahmet Yılmaz (Güncellendi)',
    'updated_at' => date('Y-m-d H:i:s')
];

$result = $db->update('users', $updateData, 'id = :id', [':id' => 1]);
```

### DELETE - Veri Silme

```php
// Tek kayıt silme
$result = $db->delete('users', 'id = :id', [':id' => 1]);

// Çoklu silme
$result = $db->delete('users', 'status = :status AND created_at < :date', [
    ':status' => 'inactive',
    ':date' => '2023-01-01'
]);
```

## 🔧 Gelişmiş Özellikler

### Transaction İşlemleri

```php
try {
    $db->beginTransaction();
    
    $userId = $db->insert('users', $userData);
    $profileId = $db->insert('user_profiles', ['user_id' => $userId, 'bio' => 'Test']);
    
    $db->commit();
    echo "Transaction başarılı";
} catch (Exception $e) {
    $db->rollback();
    echo "Hata: " . $e->getMessage();
}
```

### Özel SQL Sorguları

```php
// JOIN sorgusu
$sql = "
    SELECT u.name, u.email, p.title 
    FROM users u 
    LEFT JOIN posts p ON u.id = p.user_id 
    WHERE u.status = :status
";
$results = $db->query($sql, [':status' => 'active']);

// Aggregate functions
$sql = "SELECT COUNT(*) as total, AVG(age) as avg_age FROM users";
$stats = $db->query($sql);
```

### Dosya Yükleme

```php
// Basit dosya yükleme
$result = $db->uploadFile($_FILES['upload']);

if ($result['success']) {
    echo "Dosya yüklendi: " . $result['path'];
    
    // Veritabanına kaydet
    $db->insert('uploads', [
        'filename' => $result['filename'],
        'path' => $result['path'],
        'size' => $result['size']
    ]);
}

// Özel ayarlarla yükleme
$options = [
    'allowed_extensions' => ['jpg', 'png', 'gif'],
    'max_size' => 2 * 1024 * 1024, // 2MB
    'upload_dir' => 'images/',
    'unique_name' => true
];

$result = $db->uploadFile($_FILES['upload'], $options);
```

## 📁 Dosya Yapısı

```
pdo-god-mode/
├── pdo-god-mode-class.php    # Ana sınıf dosyası
├── examples/
│   ├── basic-crud.php        # Temel CRUD örnekleri
│   └── advanced-queries.php  # Gelişmiş sorgu örnekleri
├── uploads/                  # Dosya yükleme dizini
├── logs/                     # Log dosyaları
├── .gitignore               # Git ignore dosyası
├── LICENSE                  # Lisans dosyası
├── README.md                # Türkçe dokümantasyon (bu dosya)
├── README.en.md             # İngilizce dokümantasyon
└── schema.sql               # Veritabanı şeması
```

## 🔒 Güvenlik Özellikleri

- **SQL Injection Koruması**: Tüm sorgular prepared statements kullanır
- **XSS Koruması**: Veri sanitization
- **Dosya Yükleme Güvenliği**: Uzantı ve boyut kontrolü
- **Tablo Adı Doğrulama**: Geçersiz tablo adlarına karşı koruma
- **Hata Gizleme**: Üretim ortamında hassas bilgilerin gizlenmesi

## 📊 Performans İpuçları

### 1. Index Kullanımı
```sql
-- Sık sorgulanan sütunlara index ekleyin
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_status ON users(status);
```

### 2. Toplu İşlemler
```php
// Transaction içinde toplu insert
$db->beginTransaction();
foreach ($data as $row) {
    $db->insert('table', $row);
}
$db->commit();
```

### 3. Sayfalama
```php
// LIMIT ve OFFSET kullanın
$page = 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$users = $db->select('users', '*', '', [], 'id DESC', "{$offset}, {$perPage}");
```

## 🛠️ Hata Yönetimi

```php
// Hata kontrolü
$result = $db->insert('users', $data);
if ($result === false) {
    $error = $db->getError();
    error_log("Database error: " . $error);
}

// Exception handling
try {
    $db->insert('users', $invalidData);
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
```

## 📝 Logging ve Debugging

```php
// Query logging aktif etmek için Database sınıfını genişletebilirsiniz
// veya constructor'da logging ayarlarını yapılandırabilirsiniz
```

## 🔄 Migration Örnekleri

### Kullanıcı Tablosu
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    age INT,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Blog Tablosu
```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 🎯 Gerçek Dünya Örnekleri

### Blog Yönetim Sistemi
```php
class BlogManager {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function createPost($title, $content, $authorId) {
        return $this->db->insert('posts', [
            'title' => $title,
            'content' => $content,
            'author_id' => $authorId,
            'status' => 'draft',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function publishPost($postId) {
        return $this->db->update('posts', [
            'status' => 'published',
            'published_at' => date('Y-m-d H:i:s')
        ], 'id = :id', [':id' => $postId]);
    }
    
    public function getPublishedPosts($limit = 10) {
        return $this->db->select('posts', '*', 'status = :status', 
            [':status' => 'published'], 'published_at DESC', $limit);
    }
}
```

### E-ticaret Ürün Yönetimi
```php
class ProductManager {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function addProduct($name, $price, $stock, $categoryId) {
        return $this->db->insert('products', [
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'category_id' => $categoryId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function updateStock($productId, $quantity) {
        return $this->db->update('products', 
            ['stock' => $quantity], 
            'id = :id', 
            [':id' => $productId]
        );
    }
    
    public function getProductsByCategory($categoryId) {
        return $this->db->select('products', '*', 
            'category_id = :cat_id AND stock > 0', 
            [':cat_id' => $categoryId], 
            'name ASC'
        );
    }
}
```

## 🧪 Test Etme

```php
// Basit test
function testDatabase() {
    $db = new Database();
    
    // Insert test
    $id = $db->insert('test_table', ['name' => 'Test User']);
    assert($id > 0, 'Insert failed');
    
    // Select test
    $user = $db->selectOne('test_table', '*', 'id = :id', [':id' => $id]);
    assert($user['name'] === 'Test User', 'Select failed');
    
    // Update test
    $result = $db->update('test_table', ['name' => 'Updated'], 'id = :id', [':id' => $id]);
    assert($result === true, 'Update failed');
    
    // Delete test
    $result = $db->delete('test_table', 'id = :id', [':id' => $id]);
    assert($result === true, 'Delete failed');
    
    echo "✅ Tüm testler başarılı!";
}
```

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakın.

## 🆘 Destek

- 📧 Email: support@example.com
- 🐛 Issues: [GitHub Issues](https://github.com/username/pdo-god-mode/issues)
- 📖 Wiki: [GitHub Wiki](https://github.com/username/pdo-god-mode/wiki)

## 📈 Changelog

### v2.0.0 (2024-01-15)
- ✨ Gelişmiş hata yönetimi
- 🔒 Güvenlik iyileştirmeleri
- 📁 Gelişmiş dosya yükleme
- 🔄 Transaction desteği
- 📊 Performans optimizasyonları

### v1.0.0 (2023-12-01)
- 🎉 İlk sürüm
- ✅ Temel CRUD işlemleri
- 📁 Basit dosya yükleme

---

**PDO God Mode ile veritabanı işlemlerinizi bir üst seviyeye taşıyın! 🚀**


---

## 👨‍💻 Mimari & Geliştirici / Architected by Onur Aksoy

Bu proje **Onur Aksoy** (*Kıdemli Yazılım Mimarı & Yapay Zeka Yerel Geliştirici — a.k.a. Hackonomist*) tarafından tasarlanmış ve geliştirilmiştir.

- 🌐 **Resmi Web Sitesi & Mimari Vaka Analizleri:** [https://onuraksoy.com.tr](https://onuraksoy.com.tr)
- 🇬🇧 **English Portfolio & Architecture Case Studies:** [https://onuraksoy.com.tr/en/](https://onuraksoy.com.tr/en/)
- 💼 **LinkedIn:** [linkedin.com/in/oaonuraksoy](https://linkedin.com/in/oaonuraksoy) • **GitHub:** [@oaonuraksoy](https://github.com/oaonuraksoy)
