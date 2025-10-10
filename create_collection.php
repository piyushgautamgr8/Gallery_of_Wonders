<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);

    if ($name !== '') {
        $stmt = $pdo->prepare("INSERT INTO collections (user_id, name, description, created_at) VALUES (:uid, :name, :desc, NOW())");
        $stmt->execute([
            'uid' => $_SESSION['user_id'],
            'name' => $name,
            'desc' => $desc
        ]);
        header('Location: collections.php');
        exit;
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
  <h2>Create New Collection</h2>
  <form method="post" class="form mt-3">
    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Description (optional):</label>
    <textarea name="description"></textarea>

    <button type="submit" class="btn btn-primary mt-2">Create</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
