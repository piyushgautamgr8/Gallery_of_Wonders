<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch user collections
$stmt = $pdo->prepare("SELECT * FROM collections WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute(['uid' => $user_id]);
$collections = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
  <h2>My Collections</h2>
  <div class="text-end mb-3">
    <a href="create_collection.php" class="btn btn-primary">+ New Collection</a>
  </div>

  <?php if ($collections): ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($collections as $c): ?>
      <tr>
        <td><a href="view_collection.php?id=<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?></a></td>
        <td><?= htmlspecialchars($c['description']); ?></td>
        <td><?= htmlspecialchars($c['created_at']); ?></td>
        <td>
          <a href="edit_collection.php?id=<?= $c['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="delete_collection.php?id=<?= $c['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this collection?');">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p>You have no collections yet.</p>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
