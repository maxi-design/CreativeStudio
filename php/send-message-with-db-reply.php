<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$sender = $data['sender'] ?? '';
$content = trim($data['content'] ?? '');

if (!$sender || $content === '') {
    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
    exit;
}

if (!isset($_SESSION['chat_messages'])) {
    $_SESSION['chat_messages'] = [];
}

// Guardar el mensaje del visitante o admin en la sesión actual
$_SESSION['chat_messages'][] = [
    'sender' => $sender,
    'content' => $content
];

if ($sender === 'visitor') {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=chat_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->query("SELECT keyword, response FROM auto_responses");
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $auto_reply = '';
        foreach ($responses as $entry) {
            if (stripos($content, $entry['keyword']) !== false) {
                $auto_reply = $entry['response'];
                break;
            }
        }

        if ($auto_reply) {
            $_SESSION['chat_messages'][] = [
                'sender' => 'admin',
                'content' => $auto_reply
            ];
        }

        echo json_encode(["status" => "ok", "auto_reply" => $auto_reply]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}

if ($sender === 'admin') {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=chat_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO messages (sender, content) VALUES (?, ?)");
        $stmt->execute([$sender, $content]);

        echo json_encode(["status" => "ok", "source" => "db"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>
