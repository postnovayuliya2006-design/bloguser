<?php
session_start();
require_once "config/db.php";
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['error'=>'Не авторизован']);
    exit;
}

$post_id = (int)$_POST['post_id'];
$content = trim($_POST['content']);

if(!$content){
    echo json_encode(['error'=>'Комментарий пустой']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO comments(post_id, user_id, content) VALUES (?, ?, ?)");
$stmt->execute([$post_id, $_SESSION['user_id'], $content]);

$id = $pdo->lastInsertId();
echo json_encode([
    'id' => $id,
    'content' => htmlspecialchars($content),
    'user_name' => htmlspecialchars($_SESSION['name']),
    'created_at' => date('Y-m-d H:i:s')
]);