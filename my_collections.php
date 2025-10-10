<?php
require 'includes/db_connect.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch collections with first work as preview and work count
$stmt = $pdo->prepare("
  SELECT c.*, 
    (SELECT file_path FROM works w
      JOIN collection_items ci ON w.id = ci.work_id
      WHERE ci.collection_id = c.id
      ORDER BY ci.id ASC LIMIT 1) AS preview_image,
    (SELECT COUNT(*) FROM collection_items ci WHERE ci.collection_id=c.id) AS work_count
  FROM collections c
  WHERE c.user_id = :uid
  ORDER BY c.created_at DESC
");
$stmt->execute(['uid' => $user_id]);
$collections = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">My Collections</h2>
    <div class="text-end mb-3">
        <a href="create_collection.php" class="btn btn-primary">+ New Collection</a>
    </div>

    <?php if ($collections): ?>
        <div class="grid">
            <?php foreach ($collections as $col): ?>
                <div class="card">
                    <?php if ($col['preview_image']): ?>
                        <a href="view_collection.php?id=<?= $col['id']; ?>">
                            <img src="<?= htmlspecialchars($col['preview_image']); ?>" 
                                 style="height:150px; width:100%; object-fit:cover; border-radius:8px;">
                        </a>
                    <?php else: ?>
                        <div style="height:150px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; border-radius:8px;">
                            <span class="text-muted">No works yet</span>
                        </div>
                    <?php endif; ?>

                    <div class="p-2">
                        <h5 class="mb-1"><?= htmlspecialchars($col['name']); ?></h5>
                        <p class="text-muted small mb-1"><?= htmlspecialchars($col['description']); ?></p>
                        <p class="text-muted small mb-2"><?= $col['work_count']; ?> works</p>
                        <a href="view_collection.php?id=<?= $col['id']; ?>" class="btn btn-sm btn-primary">View Works</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No collections yet. <a href="create_collection.php">Create one now</a>.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
