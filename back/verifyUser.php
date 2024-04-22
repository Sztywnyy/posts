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
    if (isset($data['action']) && $data['action'] == 'register') {

        if (isset($data['username'], $data['password'], $data['firstname'], $data['lastname'])) {
            $username = $data['username'];
            $password = $data['password'];
            $firstname = $data['firstname'];
            $lastname = $data['lastname'];

            $query = "INSERT INTO user (username, password, firstname, lastname) VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                http_response_code(500);
                echo json_encode(array("message" => "Błąd przygotowania zapytania.", "error" => $conn->error));
                exit();
            }
            $stmt->bind_param("ssss", $username, $password, $firstname, $lastname);

            if ($stmt->execute()) {
                $last_id = $conn->insert_id;
                http_response_code(201);
                echo json_encode(array("message" => "Użytkownik został pomyślnie utworzony.", "user_id" => $last_id));
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Nie można utworzyć użytkownika.", "error" => $stmt->error));
            }

            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Niekompletne dane."));
        }
    } else {
        if (isset($data['username'], $data['password'])) {
            $username = $data['username'];
            $password = $data['password'];

            $stmt = $conn->prepare("SELECT id, password FROM user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if ($password === $user['password']) {
                    echo json_encode(['message' => 'Zalogowany', 'user_id' => $user['id']]);
                } else {
                    echo json_encode(['error' => 'Niepoprawne hasło']);
                }
            } else {
                echo json_encode(['need_registration' => true]);
            }
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Niekompletne dane."));
        }
    
    }

    // echo json_encode(array("error" => 'Wystąpił niespodziewany błąd!'));
}
$conn->close();
