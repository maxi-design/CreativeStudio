<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$keyword = trim($data['keyword'] ?? '');
$response = trim($data['response'] ?? '');

if ($keyword === '' || $response === '') {
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=chat_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO auto_responses (keyword, response) VALUES (?, ?)");
    $stmt->execute([$keyword, $response]);

    echo json_encode(["status" => "ok"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
