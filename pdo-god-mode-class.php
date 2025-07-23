<?php
/**
 * PDO God Mode Database Class
 * 
 * Gelişmiş PDO tabanlı veritabanı sınıfı
 * Güvenli, esnek ve performanslı CRUD işlemleri
 * 
 * @author Onur AKSOY A.K.A Hackonomist
 * @version 2.0
 */
class Database {
    private $host;
    private $username;
    private $password;
    private $database;
    private $charset;
    private $conn;
    private $lastInsertId;
    private $errorInfo;
    private $config;

    /**
     * Constructor - Veritabanı bağlantısını başlatır
     * 
     * @param array $config Veritabanı yapılandırma dizisi
     */
    public function __construct($config = null) {
        $this->config = $config ?: [
            'host' => 'localhost',
            'username' => 'username',
            'password' => 'password',
            'database' => 'database',
            'charset' => 'utf8mb4',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]
        ];

        $this->host = $this->config['host'];
        $this->username = $this->config['username'];
        $this->password = $this->config['password'];
        $this->database = $this->config['database'];
        $this->charset = $this->config['charset'] ?? 'utf8mb4';

        $this->connect();
    }

    /**
     * Veritabanı bağlantısını kurar
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $this->conn = new PDO($dsn, $this->username, $this->password, $this->config['options']);
        } catch(PDOException $e) {
            $this->errorInfo = $e->getMessage();
            throw new Exception("Veritabanı bağlantısı başarısız: " . $e->getMessage());
        }
    }

    /**
     * Veri ekleme işlemi
     * 
     * @param string $table Tablo adı
     * @param array $data Eklenecek veriler
     * @return bool|int Başarılıysa son eklenen ID, başarısızsa false
     */
    public function insert($table, $data) {
        try {
            $this->validateTableName($table);
            $this->validateData($data);

            $fields = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            
            $sql = "INSERT INTO `{$table}` ({$fields}) VALUES ({$placeholders})";
            $stmt = $this->conn->prepare($sql);
            
            foreach($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            $result = $stmt->execute();
            $this->lastInsertId = $this->conn->lastInsertId();
            
            return $result ? $this->lastInsertId : false;
        } catch(Exception $e) {
            $this->errorInfo = $e->getMessage();
            return false;
        }
    }

    /**
     * Veri güncelleme işlemi
     * 
     * @param string $table Tablo adı
     * @param array $data Güncellenecek veriler
     * @param string $where WHERE koşulu
     * @param array $whereParams WHERE parametreleri
     * @return bool Başarı durumu
     */
    public function update($table, $data, $where, $whereParams = []) {
        try {
            $this->validateTableName($table);
            $this->validateData($data);

            $fields = [];
            foreach($data as $key => $value) {
                $fields[] = "`{$key}` = :{$key}";
            }
            $fieldsStr = implode(", ", $fields);
            
            $sql = "UPDATE `{$table}` SET {$fieldsStr} WHERE {$where}";
            $stmt = $this->conn->prepare($sql);
            
            // Data parametrelerini bind et
            foreach($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            // WHERE parametrelerini bind et
            foreach($whereParams as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            return $stmt->execute();
        } catch(Exception $e) {
            $this->errorInfo = $e->getMessage();
            return false;
        }
    }

    /**
     * Veri silme işlemi
     * 
     * @param string $table Tablo adı
     * @param string $where WHERE koşulu
     * @param array $params WHERE parametreleri
     * @return bool Başarı durumu
     */
    public function delete($table, $where, $params = []) {
        try {
            $this->validateTableName($table);
            
            $sql = "DELETE FROM `{$table}` WHERE {$where}";
            $stmt = $this->conn->prepare($sql);
            
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            return $stmt->execute();
        } catch(Exception $e) {
            $this->errorInfo = $e->getMessage();
            return false;
        }
    }

    /**
     * Veri sorgulama işlemi
     * 
     * @param string $table Tablo adı
     * @param string $columns Seçilecek sütunlar
     * @param string $where WHERE koşulu
     * @param array $params Parametreler
     * @param string $orderBy ORDER BY koşulu
     * @param string $limit LIMIT koşulu
     * @return array|false Sonuç dizisi veya false
     */
    public function select($table, $columns = "*", $where = "", $params = [], $orderBy = "", $limit = "") {
        try {
            $this->validateTableName($table);
            
            $sql = "SELECT {$columns} FROM `{$table}`";
            
            if (!empty($where)) {
                $sql .= " WHERE {$where}";
            }
            
            if (!empty($orderBy)) {
                $sql .= " ORDER BY {$orderBy}";
            }
            
            if (!empty($limit)) {
                $sql .= " LIMIT {$limit}";
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch(Exception $e) {
            $this->errorInfo = $e->getMessage();
            return false;
        }
    }

    /**
     * Tek satır veri getirme
     * 
     * @param string $table Tablo adı
     * @param string $columns Seçilecek sütunlar
     * @param string $where WHERE koşulu
     * @param array $params Parametreler
     * @return array|false Tek satır sonuç veya false
     */
    public function selectOne($table, $columns = "*", $where = "", $params = []) {
        $result = $this->select($table, $columns, $where, $params, "", "1");
        return $result ? $result[0] : false;
    }

    /**
     * Kayıt sayısını getirme
     * 
     * @param string $table Tablo adı
     * @param string $where WHERE koşulu
     * @param array $params Parametreler
     * @return int|false Kayıt sayısı veya false
     */
    public function count($table, $where = "", $params = []) {
        try {
            $this->validateTableName($table);
            
            $sql = "SELECT COUNT(*) as count FROM `{$table}`";
            
            if (!empty($where)) {
                $sql .= " WHERE {$where}";
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch();
            return $result ? (int)$result['count'] : false;
        } catch(Exception $e) {
            $this->errorInfo = $e->getMessage();
            return false;
        }
    }

    /**
     * Özel SQL sorgusu çalıştırma
     * 
     * @param string $sql SQL sorgusu
     * @param array $params Parametreler
     * @return array|bool Sonuç dizisi veya boolean
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($params);
            
            // SELECT sorgusu ise sonuçları döndür
            if (stripos(trim($sql), 'SELECT') === 0) {
                return $stmt->fetchAll();
            }
            
            return $result;
        } catch(Exception $e) {
            $this->errorInfo = $e->getMessage();
            return false;
        }
    }

    /**
     * Transaction başlatma
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Transaction commit
     */
    public function commit() {
        return $this->conn->commit();
    }

    /**
     * Transaction rollback
     */
    public function rollback() {
        return $this->conn->rollback();
    }

    /**
     * Dosya yükleme işlemi (geliştirilmiş)
     * 
     * @param array $file $_FILES dizisinden gelen dosya
     * @param array $options Yükleme seçenekleri
     * @return array|false Başarılıysa dosya bilgileri, başarısızsa false
     */
    public function uploadFile($file, $options = []) {
        try {
            $defaultOptions = [
                'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
                'max_size' => 5 * 1024 * 1024, // 5MB
                'upload_dir' => 'uploads/',
                'create_date_folder' => true,
                'unique_name' => true
            ];
            
            $options = array_merge($defaultOptions, $options);
            
            // Dosya kontrolü
            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                throw new Exception("Geçersiz dosya");
            }
            
            // Boyut kontrolü
            if ($file['size'] > $options['max_size']) {
                throw new Exception("Dosya boyutu çok büyük");
            }
            
            // Uzantı kontrolü
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $options['allowed_extensions'])) {
                throw new Exception("İzin verilmeyen dosya türü");
            }
            
            // Yükleme dizini oluşturma
            $uploadDir = $options['upload_dir'];
            if ($options['create_date_folder']) {
                $uploadDir .= date("Y/m/d") . '/';
            }
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Dosya adı belirleme
            $filename = $options['unique_name'] 
                ? uniqid() . '_' . time() . '.' . $extension
                : $file['name'];
            
            $uploadPath = $uploadDir . $filename;
            
            // Dosyayı taşı
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return [
                    'success' => true,
                    'path' => $uploadPath,
                    'filename' => $filename,
                    'original_name' => $file['name'],
                    'size' => $file['size'],
                    'extension' => $extension
                ];
            } else {
                throw new Exception("Dosya yüklenemedi");
            }
            
        } catch(Exception $e) {
            $this->errorInfo = $e->getMessage();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Tablo adı doğrulama
     */
    private function validateTableName($table) {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            throw new Exception("Geçersiz tablo adı");
        }
    }

    /**
     * Veri doğrulama
     */
    private function validateData($data) {
        if (empty($data) || !is_array($data)) {
            throw new Exception("Geçersiz veri");
        }
    }

    /**
     * Son eklenen ID'yi getir
     */
    public function getLastInsertId() {
        return $this->lastInsertId;
    }

    /**
     * Hata bilgisini getir
     */
    public function getError() {
        return $this->errorInfo;
    }

    /**
     * Bağlantıyı kapat
     */
    public function close() {
        $this->conn = null;
    }

    /**
     * Destructor
     */
    public function __destruct() {
        $this->close();
    }
}



?>