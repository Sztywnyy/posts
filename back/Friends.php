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

        if (isset($data['firstname'], $data['lastname'], $data['user'])) {
            $firstname = $data['firstname'];
            $lastname = $data['lastname'];
            $user = $data['user'];
            $sql = "SELECT id FROM user WHERE firstname = ? AND lastname = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $firstname, $lastname);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if ($result !== null) {
                $query = "INSERT INTO friends (user_id, friend_id) VALUES (?, ?)";
                $stmt = $conn->prepare($query);
                if (!$stmt) {
                    http_response_code(500);
                    echo json_encode(array("message" => "Błąd przygotowania zapytania.", "error" => $conn->error));
                    exit();
                }
                $stmt->bind_param("ss", $user, $result['id']);
    
                if ($stmt->execute()) {
                    $last_id = $conn->insert_id;
                    http_response_code(201);
                    echo json_encode(array("message" => "Użytkownik został pomyślnie dodany."));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Nie można dodać użytkownika.", "error" => $stmt->error));
                }
              } else {
                echo "No result found";
              }

            $stmt->close(); 
            // if($result->num_rows ==0){
            // echo json_encode(array("message" => "Nie znaleziono użytkownika"));
            // exit; 
            // }
            

            // $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Niekompletne dane."));
        }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['user'])) {
        $user = $_GET['user'];
        $sql = "SELECT u.id, u.firstname, u.lastname FROM friends f JOIN user u ON f.friend_id = u.id WHERE f.user_id = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(array("message" => "Błąd przygotowania zapytania.", "error" => $conn->error));
            exit();
        }

        $stmt->bind_param("i", $user);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $friends = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(array("friends" => $friends));
        } else {
            echo json_encode(array("message" => "Nie znaleziono przyjaciół."));
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Brak parametru 'user' w zapytaniu."));
    }
}
$conn->close();