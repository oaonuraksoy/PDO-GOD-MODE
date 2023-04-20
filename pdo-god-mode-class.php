<?php 
class Database {
    private $host = "localhost";
    private $username = "username";
    private $password = "password";
    private $database = "database";
    private $conn;

    public function __construct() {
        try {
            $dsn = "mysql:host=$this->host;dbname=$this->database";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function insert($table, $data) {
        $fields = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($fields) VALUES ($values)";
        $stmt = $this->conn->prepare($sql);
        foreach($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        return $stmt->execute();
    }

    public function update($table, $data, $id) {
        $fields = '';
        foreach($data as $key => $value) {
            $fields .= "$key=:$key, ";
        }
        $fields = rtrim($fields, ', ');
        $sql = "UPDATE $table SET $fields WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        foreach($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($table, $id) {
        $sql = "DELETE FROM $table WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function uploadImage($file) {
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if(!in_array($extension, $allowedExtensions)) {
            return false;
        }

        $uploadDir = 'upload/' . date("Y-m-d") . '/';
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid() . '.' . $extension;
        $uploadFile = $uploadDir . $filename;

        if(move_uploaded_file($file['tmp_name'], $uploadFile)) {
            return array('path' => $uploadFile, 'filename' => $filename);
        } else {
            return false;
        }
    }

    public function getData($table, $columns = "*", $where = "", $params = array()) {
        $sql = "SELECT $columns FROM $table";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}



?>