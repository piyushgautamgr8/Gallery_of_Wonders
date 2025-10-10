<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'error' => 'Not logged in']);
  exit;
}

$user_id = $_SESSION['user_id'];
$work_id = (int)($_POST['work_id'] ?? 0);

if (!$work_id) {
  echo json_encode(['success' => false, 'error' => 'Invalid work']);
  exit;
}

// Check if already saved
$check = $pdo->prepare("SELECT id FROM saved_works WHERE user_id = :uid AND work_id = :wid");
$check->execute(['uid' => $user_id, 'wid' => $work_id]);
$exists = $check->fetch();

if ($exists) {
  // remove
  $pdo->prepare("DELETE FROM saved_works WHERE user_id = :uid AND work_id = :wid")->execute(['uid' => $user_id, 'wid' => $work_id]);
  echo json_encode(['success' => true, 'saved' => false]);
} else {
  // save
  $pdo->prepare("INSERT INTO saved_works (user_id, work_id) VALUES (:uid, :wid)")->execute(['uid' => $user_id, 'wid' => $work_id]);
  echo json_encode(['success' => true, 'saved' => true]);
}
