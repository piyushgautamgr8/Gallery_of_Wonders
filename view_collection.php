<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: collections.php');
    exit;
}

// Fetch collection details
$stmt = $pdo->prepare("SELECT * FROM collections WHERE id = :id");
$stmt->execute(['id' => $id]);
$collection = $stmt->fetch();
if (!$collection) {
    header('Location: collections.php');
    exit;
}

// Handle remove action (only if logged in & owner)
if (isset($_GET['remove']) && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $collection['user_id']) {
    $wid = (int)$_GET['remove'];
    $del = $pdo->prepare("DELETE FROM collection_items WHERE collection_id = :cid AND work_id = :wid");
    $del->execute(['cid' => $id, 'wid' => $wid]);
    header("Location: view_collection.php?id=$id");
    exit;
}

// Fetch works in this collection
$stmt = $pdo->prepare("
  SELECT w.*, u.username
  FROM collection_items ci
  JOIN works w ON ci.work_id = w.id
  JOIN users u ON w.user_id = u.id
  WHERE ci.collection_id = :cid
  ORDER BY ci.id DESC
");
$stmt->execute(['cid' => $id]);
$works = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
  <h2 class="mb-2"><?= htmlspecialchars($collection['name']); ?></h2>
  <p class="text-muted"><?= htmlspecialchars($collection['description']); ?></p>
  <hr>

  <?php if (empty($works)): ?>
    <p>No works in this collection yet.</p>
  <?php else: ?>
    <div class="row">
      <?php foreach ($works as $w): ?>
        <div class="col-md-3 col-sm-6 mb-4">
          <div class="card shadow-sm border-0">
            <a href="view_work.php?id=<?= $w['id']; ?>">
              <img src="<?= htmlspecialchars($w['file_path']); ?>" 
                   alt="<?= htmlspecialchars($w['title']); ?>" 
                   class="card-img-top" 
                   style="height:200px; object-fit:cover; border-radius:8px;">
            </a>
            <div class="card-body text-center">
              <h6 class="card-title mb-1"><?= htmlspecialchars($w['title']); ?></h6>
              <p class="text-muted small mb-2"><?= htmlspecialchars($w['category']); ?></p>

              <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $collection['user_id']): ?>
                <a href="view_collection.php?id=<?= $id; ?>&remove=<?= $w['id']; ?>"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Remove this work from collection?');">
                   Remove
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
