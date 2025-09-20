<?php
// SQLite database connection for Replit environment
$db_path = __DIR__ . '/gabaylakad_db.sqlite';

try {
    // Use SQLite with PDO
    $pdo = new PDO("sqlite:$db_path");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create a mysqli-compatible wrapper for existing code
    $conn = new SQLiteConnection($pdo);
} catch(PDOException $e) {
    die("Failed to connect to database: " . $e->getMessage());
}

// SQLite wrapper class to maintain mysqli compatibility
class SQLiteConnection {
    private $pdo;
    public $insert_id;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function prepare($sql) {
        // Convert MySQLi placeholders to PDO placeholders if needed
        $sql = str_replace('NOW()', 'datetime("now")', $sql);
        return new SQLiteStatement($this->pdo->prepare($sql), $this);
    }
    
    public function begin_transaction() {
        return $this->pdo->beginTransaction();
    }
    
    public function commit() {
        return $this->pdo->commit();
    }
    
    public function rollback() {
        return $this->pdo->rollBack();
    }
    
    public function set_charset($charset) {
        // SQLite handles UTF-8 by default
        return true;
    }
}

class SQLiteStatement {
    private $stmt;
    private $conn;
    
    public function __construct($stmt, $conn) {
        $this->stmt = $stmt;
        $this->conn = $conn;
    }
    
    public function bind_param($types, ...$params) {
        // Store parameters for execute
        $this->params = $params;
        return true;
    }
    
    public function execute() {
        $result = $this->stmt->execute($this->params ?? []);
        $this->conn->insert_id = $this->conn->pdo->lastInsertId();
        return $result;
    }
    
    public function get_result() {
        return new SQLiteResult($this->stmt);
    }
    
    public function close() {
        $this->stmt = null;
        return true;
    }
}

class SQLiteResult {
    private $stmt;
    public $num_rows;
    
    public function __construct($stmt) {
        $this->stmt = $stmt;
        $this->num_rows = $this->stmt->rowCount();
    }
    
    public function fetch_assoc() {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>