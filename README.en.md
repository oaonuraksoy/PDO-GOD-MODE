# PDO God Mode Database Class 🚀

**[🇹🇷 Türkçe okumak için buraya tıklayın / Click here to read in Turkish](README.md)**

**Powerful, Secure and Flexible PHP PDO Database Class**

PDO God Mode is a secure and performant PDO wrapper class that simplifies database operations in your PHP projects. It works with any table structure and offers advanced features.

## ✨ Features

- 🔒 **Secure**: SQL Injection protection with prepared statements
- 🚀 **Performant**: Optimized queries and connection pooling
- 🔧 **Flexible**: Works with any table structure
- 📁 **File Upload**: Advanced file upload system
- 🔄 **Transaction Support**: ACID compliant operations
- 📊 **Advanced Queries**: JOIN, subquery, aggregate functions
- 🛠️ **Error Handling**: Comprehensive error catching and reporting
- 📝 **Logging**: Query logging and debugging
- 🎯 **Easy to Use**: Maximum functionality with minimal code

## 📋 Requirements

- PHP 7.4 or higher
- PDO extension
- MySQL 5.7+ or MariaDB 10.2+

## 🚀 Installation

1. Copy files to your project:
```bash
git clone https://github.com/username/pdo-god-mode.git
```

2. Use the class in your project:
```php
require_once 'pdo-god-mode-class.php';
$db = new Database($config);
```

## 📖 Basic Usage

### Database Connection

```php
// With default settings
$db = new Database();

// With custom settings
$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'test_db',
    'charset' => 'utf8mb4'
];
$db = new Database($config);
```

### CREATE - Insert Data

```php
// Simple insert
$userData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1-555-123-4567'
];

$userId = $db->insert('users', $userData);
if ($userId) {
    echo "User added. ID: {$userId}";
}
```

### READ - Read Data

```php
// Get all records
$users = $db->select('users');

// Conditional queries
$activeUsers = $db->select('users', '*', 'status = :status', [':status' => 'active']);

// Get single record
$user = $db->selectOne('users', '*', 'id = :id', [':id' => 1]);

// Count records
$userCount = $db->count('users', 'status = :status', [':status' => 'active']);

// Sorting and limit
$recentUsers = $db->select('users', '*', '', [], 'created_at DESC', '10');
```

### UPDATE - Update Data

```php
$updateData = [
    'name' => 'John Doe (Updated)',
    'updated_at' => date('Y-m-d H:i:s')
];

$result = $db->update('users', $updateData, 'id = :id', [':id' => 1]);
```

### DELETE - Delete Data

```php
// Delete single record
$result = $db->delete('users', 'id = :id', [':id' => 1]);

// Multiple delete
$result = $db->delete('users', 'status = :status AND created_at < :date', [
    ':status' => 'inactive',
    ':date' => '2023-01-01'
]);
```

## 🔧 Advanced Features

### Transaction Operations

```php
try {
    $db->beginTransaction();
    
    $userId = $db->insert('users', $userData);
    $profileId = $db->insert('user_profiles', ['user_id' => $userId, 'bio' => 'Test']);
    
    $db->commit();
    echo "Transaction successful";
} catch (Exception $e) {
    $db->rollback();
    echo "Error: " . $e->getMessage();
}
```

### Custom SQL Queries

```php
// JOIN query
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

### File Upload

```php
// Simple file upload
$result = $db->uploadFile($_FILES['upload']);

if ($result['success']) {
    echo "File uploaded: " . $result['path'];
    
    // Save to database
    $db->insert('uploads', [
        'filename' => $result['filename'],
        'path' => $result['path'],
        'size' => $result['size']
    ]);
}

// Upload with custom options
$options = [
    'allowed_extensions' => ['jpg', 'png', 'gif'],
    'max_size' => 2 * 1024 * 1024, // 2MB
    'upload_dir' => 'images/',
    'unique_name' => true
];

$result = $db->uploadFile($_FILES['upload'], $options);
```

## 📁 File Structure

```
pdo-god-mode/
├── pdo-god-mode-class.php    # Main class file
├── examples/
│   ├── basic-crud.php        # Basic CRUD examples
│   └── advanced-queries.php  # Advanced query examples
├── uploads/                  # File upload directory
├── logs/                     # Log files
├── .gitignore               # Git ignore file
├── LICENSE                  # License file
├── README.md                # Turkish documentation
├── README.en.md             # English documentation (this file)
└── schema.sql               # Database schema
```

## 🔒 Security Features

- **SQL Injection Protection**: All queries use prepared statements
- **XSS Protection**: Data sanitization
- **File Upload Security**: Extension and size validation
- **Table Name Validation**: Protection against invalid table names
- **Error Hiding**: Sensitive information hidden in production

## 📊 Performance Tips

### 1. Index Usage
```sql
-- Add indexes to frequently queried columns
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_status ON users(status);
```

### 2. Batch Operations
```php
// Batch insert within transaction
$db->beginTransaction();
foreach ($data as $row) {
    $db->insert('table', $row);
}
$db->commit();
```

### 3. Pagination
```php
// Use LIMIT and OFFSET
$page = 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$users = $db->select('users', '*', '', [], 'id DESC', "{$offset}, {$perPage}");
```

## 🛠️ Error Handling

```php
// Error checking
$result = $db->insert('users', $data);
if ($result === false) {
    $error = $db->getError();
    error_log("Database error: " . $error);
}

// Exception handling
try {
    $db->insert('users', $invalidData);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## 📝 Logging and Debugging

```php
// Enable query logging by extending the Database class
// or configure logging settings in the constructor
```

## 🔄 Migration Examples

### Users Table
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

### Blog Table
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

## 🎯 Real World Examples

### Blog Management System
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

### E-commerce Product Management
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

## 🧪 Testing

```php
// Simple test
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
    
    echo "✅ All tests passed!";
}
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License. See the `LICENSE` file for details.

## 🆘 Support

- 📧 Email: support@example.com
- 🐛 Issues: [GitHub Issues](https://github.com/username/pdo-god-mode/issues)
- 📖 Wiki: [GitHub Wiki](https://github.com/username/pdo-god-mode/wiki)

## 📈 Changelog

### v2.0.0 (2024-01-15)
- ✨ Enhanced error handling
- 🔒 Security improvements
- 📁 Advanced file upload
- 🔄 Transaction support
- 📊 Performance optimizations

### v1.0.0 (2023-12-01)
- 🎉 Initial release
- ✅ Basic CRUD operations
- 📁 Simple file upload

---

**Take your database operations to the next level with PDO God Mode! 🚀**