<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Credentials: true");

include_once 'Database.php';

$database = new Database('localhost', 'praktyki', 'root', '');
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'];
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT id, password FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            setcookie("user_id", $user['id'], time() + (86400 * 7), "/");
            echo json_encode(['message' => 'Zalogowany', 'user_id' => $user['id'], 'cookievalue' => $_COOKIE['user_id']]);
        } else {
            echo json_encode(['error' => 'Niepoprawne hasło']);
        }
    } else {
        echo json_encode(['need_registration' => true]);
    }
    $stmt->close();
}
$conn->close();
?>
