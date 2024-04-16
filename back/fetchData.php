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
    $conn = $database->getConnection();
    $sql = "SELECT p.id, p.title, p.content, u.username, u.firstname, u.lastname FROM post p LEFT JOIN user u ON p.user_id = u.id";
    $result = $conn->query($sql);
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['posts' => $posts]);
}
?>