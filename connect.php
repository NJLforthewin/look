<?php
// SQLite database connection for Replit environment
$db_path = __DIR__ . '/gabaylakad_db.sqlite';

try {
    // Use SQLite with PDO
    $pdo = new PDO("sqlite:$db_path");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Failed to connect to database: " . $e->getMessage());
}

// SQLite wrapper class to maintain mysqli compatibility
class SQLiteConnection {
    public $pdo;
    public $insert_id;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->insert_id = 0;
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
    private $params = [];
    
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
        try {
            $result = $this->stmt->execute($this->params);
            $this->conn->insert_id = $this->conn->pdo->lastInsertId();
            return $result;
        } catch(PDOException $e) {
            return false;
        }
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
    private $data;
    private $index;
    
    public function __construct($stmt) {
        $this->stmt = $stmt;
        // For SQLite, we need to fetch all results to get accurate row count
        $this->data = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->num_rows = count($this->data);
        $this->index = 0;
    }
    
    public function fetch_assoc() {
        if ($this->index < count($this->data)) {
            return $this->data[$this->index++];
        }
        return false;
    }
}

// Create the connection instance
$conn = new SQLiteConnection($pdo);
?>