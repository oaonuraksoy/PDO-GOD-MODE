-- PDO God Mode Database Schema
-- Bu dosya örnek tablo yapılarını içerir

-- Veritabanı oluştur
CREATE DATABASE IF NOT EXISTS test_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE test_db;

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    age INT,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    avatar VARCHAR(500),
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_users_email (email),
    INDEX idx_users_status (status),
    INDEX idx_users_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kategoriler tablosu
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_categories_slug (slug),
    INDEX idx_categories_parent (parent_id),
    INDEX idx_categories_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog yazıları tablosu
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT,
    featured_image VARCHAR(500),
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    like_count INT DEFAULT 0,
    comment_count INT DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    tags JSON,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_posts_user (user_id),
    INDEX idx_posts_category (category_id),
    INDEX idx_posts_status (status),
    INDEX idx_posts_published (published_at),
    INDEX idx_posts_slug (slug),
    FULLTEXT idx_posts_search (title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Yorumlar tablosu
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT,
    parent_id INT NULL,
    author_name VARCHAR(255),
    author_email VARCHAR(255),
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'spam') DEFAULT 'pending',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_comments_post (post_id),
    INDEX idx_comments_user (user_id),
    INDEX idx_comments_parent (parent_id),
    INDEX idx_comments_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ürünler tablosu (E-ticaret örneği)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(500) NOT NULL,
    slug VARCHAR(500) UNIQUE NOT NULL,
    description TEXT,
    short_description TEXT,
    sku VARCHAR(100) UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    stock INT DEFAULT 0,
    min_stock INT DEFAULT 0,
    weight DECIMAL(8,2),
    dimensions JSON, -- {"length": 10, "width": 5, "height": 3}
    images JSON, -- ["image1.jpg", "image2.jpg"]
    attributes JSON, -- {"color": "red", "size": "L"}
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    sale_count INT DEFAULT 0,
    rating_average DECIMAL(3,2) DEFAULT 0.00,
    rating_count INT DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_products_category (category_id),
    INDEX idx_products_sku (sku),
    INDEX idx_products_slug (slug),
    INDEX idx_products_status (status),
    INDEX idx_products_price (price),
    INDEX idx_products_stock (stock),
    FULLTEXT idx_products_search (name, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Siparişler tablosu
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    shipping_amount DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'TRY',
    
    -- Billing Address
    billing_first_name VARCHAR(100),
    billing_last_name VARCHAR(100),
    billing_company VARCHAR(200),
    billing_address_1 VARCHAR(500),
    billing_address_2 VARCHAR(500),
    billing_city VARCHAR(100),
    billing_state VARCHAR(100),
    billing_postcode VARCHAR(20),
    billing_country VARCHAR(2),
    billing_email VARCHAR(255),
    billing_phone VARCHAR(20),
    
    -- Shipping Address
    shipping_first_name VARCHAR(100),
    shipping_last_name VARCHAR(100),
    shipping_company VARCHAR(200),
    shipping_address_1 VARCHAR(500),
    shipping_address_2 VARCHAR(500),
    shipping_city VARCHAR(100),
    shipping_state VARCHAR(100),
    shipping_postcode VARCHAR(20),
    shipping_country VARCHAR(2),
    
    notes TEXT,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_orders_user (user_id),
    INDEX idx_orders_number (order_number),
    INDEX idx_orders_status (status),
    INDEX idx_orders_payment_status (payment_status),
    INDEX idx_orders_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sipariş ürünleri tablosu
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(500) NOT NULL,
    product_sku VARCHAR(100),
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    product_data JSON, -- Ürün bilgilerinin snapshot'ı
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_order_items_order (order_id),
    INDEX idx_order_items_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dosya yüklemeleri tablosu
CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    filename VARCHAR(500) NOT NULL,
    original_name VARCHAR(500) NOT NULL,
    path VARCHAR(1000) NOT NULL,
    url VARCHAR(1000),
    mime_type VARCHAR(100),
    size INT NOT NULL,
    width INT,
    height INT,
    alt_text VARCHAR(500),
    description TEXT,
    is_public BOOLEAN DEFAULT TRUE,
    download_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_uploads_user (user_id),
    INDEX idx_uploads_filename (filename),
    INDEX idx_uploads_mime (mime_type),
    INDEX idx_uploads_public (is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ayarlar tablosu
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(255) UNIQUE NOT NULL,
    value LONGTEXT,
    type ENUM('string', 'number', 'boolean', 'json', 'text') DEFAULT 'string',
    group_name VARCHAR(100) DEFAULT 'general',
    description TEXT,
    is_autoload BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_settings_key (key_name),
    INDEX idx_settings_group (group_name),
    INDEX idx_settings_autoload (is_autoload)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log tablosu
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    level ENUM('emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug') NOT NULL,
    message TEXT NOT NULL,
    context JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    url VARCHAR(1000),
    method VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_logs_user (user_id),
    INDEX idx_logs_level (level),
    INDEX idx_logs_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Oturumlar tablosu
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sessions_user (user_id),
    INDEX idx_sessions_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Örnek veri ekleme
INSERT INTO users (name, email, phone, age, status, bio) VALUES
('Ahmet Yılmaz', 'ahmet@example.com', '0555-123-4567', 28, 'active', 'Web developer ve blog yazarı'),
('Ayşe Demir', 'ayse@example.com', '0555-234-5678', 25, 'active', 'Grafik tasarımcı'),
('Mehmet Kaya', 'mehmet@example.com', '0555-345-6789', 32, 'active', 'Proje yöneticisi'),
('Fatma Özkan', 'fatma@example.com', '0555-456-7890', 29, 'inactive', 'İçerik editörü'),
('Ali Çelik', 'ali@example.com', '0555-567-8901', 35, 'active', 'Sistem yöneticisi');

INSERT INTO categories (name, slug, description) VALUES
('Teknoloji', 'teknoloji', 'Teknoloji ile ilgili yazılar'),
('Yazılım', 'yazilim', 'Yazılım geliştirme konuları'),
('Tasarım', 'tasarim', 'Web ve grafik tasarım'),
('İş Dünyası', 'is-dunyasi', 'İş ve kariyer konuları'),
('Yaşam', 'yasam', 'Günlük yaşam ve kişisel gelişim');

INSERT INTO posts (user_id, category_id, title, slug, content, status, published_at) VALUES
(1, 2, 'PHP ile Modern Web Geliştirme', 'php-ile-modern-web-gelistirme', 'PHP ile modern web uygulamaları geliştirme teknikleri...', 'published', NOW()),
(2, 3, 'UI/UX Tasarım Prensipleri', 'ui-ux-tasarim-prensipleri', 'Kullanıcı deneyimi odaklı tasarım prensipleri...', 'published', NOW()),
(3, 4, 'Proje Yönetiminde Agile Metodoloji', 'proje-yonetiminde-agile-metodoloji', 'Agile proje yönetimi teknikleri ve uygulamaları...', 'published', NOW()),
(1, 1, 'Yapay Zeka ve Gelecek', 'yapay-zeka-ve-gelecek', 'Yapay zekanın günlük yaşamımıza etkileri...', 'draft', NULL),
(5, 1, 'Siber Güvenlik Temelleri', 'siber-guvenlik-temelleri', 'Temel siber güvenlik önlemleri ve uygulamaları...', 'published', NOW());

INSERT INTO products (category_id, name, slug, price, stock, status) VALUES
(1, 'Laptop Bilgisayar', 'laptop-bilgisayar', 15000.00, 10, 'active'),
(1, 'Akıllı Telefon', 'akilli-telefon', 8000.00, 25, 'active'),
(3, 'Grafik Tablet', 'grafik-tablet', 2500.00, 5, 'active'),
(2, 'Programlama Kitabı', 'programlama-kitabi', 150.00, 50, 'active'),
(1, 'Wireless Mouse', 'wireless-mouse', 200.00, 100, 'active');

INSERT INTO settings (key_name, value, type, group_name, description) VALUES
('site_title', 'PDO God Mode Demo', 'string', 'general', 'Site başlığı'),
('site_description', 'Güçlü PDO veritabanı sınıfı demo sitesi', 'string', 'general', 'Site açıklaması'),
('posts_per_page', '10', 'number', 'blog', 'Sayfa başına gösterilecek yazı sayısı'),
('enable_comments', 'true', 'boolean', 'blog', 'Yorumları etkinleştir'),
('contact_email', 'info@example.com', 'string', 'contact', 'İletişim e-posta adresi');

-- Trigger örnekleri
DELIMITER //

-- Post sayacını güncelle
CREATE TRIGGER update_comment_count 
AFTER INSERT ON comments 
FOR EACH ROW
BEGIN
    UPDATE posts 
    SET comment_count = (
        SELECT COUNT(*) 
        FROM comments 
        WHERE post_id = NEW.post_id AND status = 'approved'
    ) 
    WHERE id = NEW.post_id;
END//

-- Stok kontrolü
CREATE TRIGGER check_stock_before_order 
BEFORE INSERT ON order_items 
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    
    SELECT stock INTO current_stock 
    FROM products 
    WHERE id = NEW.product_id;
    
    IF current_stock < NEW.quantity THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Yetersiz stok!';
    END IF;
END//

DELIMITER ;

-- View örnekleri
CREATE VIEW active_posts AS
SELECT 
    p.id,
    p.title,
    p.slug,
    p.excerpt,
    p.published_at,
    u.name as author_name,
    c.name as category_name,
    p.view_count,
    p.comment_count
FROM posts p
JOIN users u ON p.user_id = u.id
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.status = 'published'
ORDER BY p.published_at DESC;

CREATE VIEW user_stats AS
SELECT 
    u.id,
    u.name,
    u.email,
    COUNT(p.id) as post_count,
    SUM(p.view_count) as total_views,
    SUM(p.comment_count) as total_comments
FROM users u
LEFT JOIN posts p ON u.id = p.user_id AND p.status = 'published'
GROUP BY u.id, u.name, u.email;

-- Index optimizasyonları
ANALYZE TABLE users, posts, products, orders;
OPTIMIZE TABLE users, posts, products, orders;

-- Yedekleme için örnek komut (yorum olarak)
-- mysqldump -u username -p test_db > backup.sql

SHOW TABLES;
SELECT 'Database schema created successfully!' as message;