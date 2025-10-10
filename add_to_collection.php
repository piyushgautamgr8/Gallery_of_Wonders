<?php
require 'includes/db_connect.php';
if (session_status()===PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if(!isset($_SESSION['user_id'],$data['collection_id'],$data['work_id'])) exit(json_encode(['success'=>false]));

$cid = (int)$data['collection_id'];
$wid = (int)$data['work_id'];
$uid = $_SESSION['user_id'];

// Verify ownership
$stmt = $pdo->prepare("SELECT * FROM collections WHERE id=:cid AND user_id=:uid");
$stmt->execute(['cid'=>$cid,'uid'=>$uid]);
if(!$stmt->fetch()) exit(json_encode(['success'=>false]));

// Insert if not exists
$stmt2 = $pdo->prepare("SELECT * FROM collection_items WHERE collection_id=:cid AND work_id=:wid");
$stmt2->execute(['cid'=>$cid,'wid'=>$wid]);
if(!$stmt2->fetch()){
    $stmt3 = $pdo->prepare("INSERT INTO collection_items (collection_id, work_id) VALUES (:cid,:wid)");
    $stmt3->execute(['cid'=>$cid,'wid'=>$wid]);
}

echo json_encode(['success'=>true]);
