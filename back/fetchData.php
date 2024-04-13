<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Respond with ok status for preflight requests (no further action needed)
    http_response_code(200);
    exit(0);
}

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
            $stmt = $this->getConnection()->prepare("INSERT INTO post (title, content) VALUES (?, ?)");
            $stmt->bind_param("ss", $data['title'], $data['content']);
            $stmt->execute();
        
            if ($stmt->affected_rows > 0) {
                echo json_encode(['message' => 'Post added successfully']);
            } else {
                echo json_encode(['error' => 'Failed to add post']);
            }
            $stmt->close();
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

$database = new Database('localhost', 'praktyki', 'root', '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database->addPost();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $database->deletePost();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = $database->getConnection();
    $result = $conn->query("SELECT id, title, content FROM post");
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['posts' => $posts]);
}
?>
