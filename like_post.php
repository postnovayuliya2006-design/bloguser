<?php
session_start();
require_once "config/db.php";
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['error'=>'Не авторизован']);
    exit;
}

$post_id = (int)$_POST['post_id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT 1 FROM post_likes WHERE post_id=? AND user_id=?");
$stmt->execute([$post_id, $user_id]);

if($stmt->fetchColumn()){
    $pdo->prepare("DELETE FROM post_likes WHERE post_id=? AND user_id=?")->execute([$post_id, $user_id]);
} else {
    $pdo->prepare("INSERT INTO post_likes(post_id, user_id) VALUES(?, ?)")->execute([$post_id, $user_id]);
}

$count = $pdo->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id=?");
$count->execute([$post_id]);
$count = $count->fetchColumn();

echo json_encode(['count'=>$count]);