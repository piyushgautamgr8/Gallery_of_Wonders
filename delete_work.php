<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$work_id = $_GET['id'] ?? null;

if (!$work_id) {
    die("Invalid work ID");
}

// Fetch work to verify ownership
$stmt = $pdo->prepare("SELECT * FROM works WHERE id = :id AND user_id = :uid");
$stmt->execute(['id' => $work_id, 'uid' => $user_id]);
$work = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$work) {
    die("Work not found or you don’t have permission to delete it.");
}

// Delete file from server
if (!empty($work['file_path']) && file_exists($work['file_path'])) {
    unlink($work['file_path']);
}

// Delete record from database
$delete = $pdo->prepare("DELETE FROM works WHERE id = :id AND user_id = :uid");
$delete->execute(['id' => $work_id, 'uid' => $user_id]);

// Redirect to dashboard with success message
header("Location: dashboard.php");
exit();
?>
