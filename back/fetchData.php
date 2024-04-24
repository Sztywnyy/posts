<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

require_once 'Database.php';

$database = new Database('localhost', 'praktyki', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database->addPost();
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $database->deletePost();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(!isset($_GET["user"])) {
        $conn = $database->getConnection();
    $sql = "SELECT p.id, p.title, p.content, u.username, u.firstname, u.lastname FROM post p LEFT JOIN user u ON p.user_id = u.id";
    $result = $conn->query($sql);
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['posts' => $posts]);
        exit;
    };
    $user = $_GET['user'];
    $conn = $database->getConnection();
    $sql = "SELECT p.id, p.title, p.content, u.username, u.firstname, u.lastname FROM post p LEFT JOIN user u ON p.user_id = u.id WHERE p.user_id =".$user;
    $result = $conn->query($sql);
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['posts' => $posts]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!empty($data['id']) && isset($data['title']) && isset($data['content'])) {
        $id = $data['id'];
        $title = $data['title'];
        $content = $data['content'];
    
        $query = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
    
        if (!$stmt) {
            echo json_encode(['error' => 'Błąd przygotowania zapytania: ' . $conn->error]);
            exit;
        }
    
        $stmt->bind_param("ssi", $title, $content, $id);
    
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Post został zaktualizowany.']);
        } else {
            echo json_encode(['error' => 'Nie udało się zaktualizować posta: ' . $stmt->error]);
        }
    
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Niekompletne dane.']);
    }
    
    $conn->close();
} else {
    echo json_encode(['error'=>'unknown method']);
}
?>