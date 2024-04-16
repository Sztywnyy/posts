<?php
class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    public $conn;

    public function __construct($host, $dbname, $username, $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->conn = null;
    }

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
                $this->conn->set_charset("utf8mb4");
            } catch (Exception $exception) {
                echo json_encode(['error' => "Connection Error: " . $exception->getMessage()]);
                exit(0);
            }
        }
        return $this->conn;
    }

    public function addPost() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data)) {
            echo json_encode(['error' => 'No data provided']);
            exit;
        } else {
            if(!isset($_COOKIE['user_id'])) {
                echo json_encode(['error' => 'Not logged!']);
                exit;
            }
            try {
                $stmt = $this->getConnection()->prepare("INSERT INTO post (title, content, user_id) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $data['title'], $data['content'], $_COOKIE['user_id']);
                $stmt->execute();
    
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['message' => 'Post added successfully']);
                } else {
                    echo json_encode(['error' => 'Failed to add post']);
                }
                $stmt->close();
            } catch(Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            } 
        }
    }
    
    public function deletePost() {
        $id = $_GET['id'];
        $stmt = $this->getConnection()->prepare("DELETE FROM post WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            echo json_encode(['message' => 'Post deleted successfully']);
        } else {
            echo json_encode(['error' => 'Failed to delete post']);
        }
        $stmt->close();
    } 
}
?>
