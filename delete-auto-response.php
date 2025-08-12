<?php
header('Content-Type: application/json');

// Intentar obtener el ID tanto desde JSON como desde POST
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

$id = 0;
if (is_array($data) && isset($data['id'])) {
    $id = (int) $data['id'];
} elseif (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
}

if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "ID inválido"]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=chat_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM auto_responses WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["status" => "ok"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>



