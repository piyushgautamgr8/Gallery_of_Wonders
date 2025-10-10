<?php
require 'includes/db_connect.php';
session_start();
header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'] ?? 0;
$work_id = (int)($_POST['work_id'] ?? 0);

if(!$user_id || !$work_id) {
    echo json_encode(['success'=>false]); exit;
}

// prevent duplicate
$stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id=:uid AND work_id=:wid");
$stmt->execute(['uid'=>$user_id,'wid'=>$work_id]);
if(!$stmt->fetch()){
    $stmt = $pdo->prepare("INSERT INTO likes (user_id, work_id) VALUES (:uid,:wid)");
    $stmt->execute(['uid'=>$user_id,'wid'=>$work_id]);
}

echo json_encode(['success'=>true]);
