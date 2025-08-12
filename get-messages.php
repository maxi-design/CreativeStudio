<?php
session_start();
$messages = $_SESSION['chat_messages'] ?? [];
echo json_encode($messages);
?>



