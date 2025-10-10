<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
  SELECT w.id, w.title, w.file_path, w.category, u.username
  FROM saved_works s
  JOIN works w ON s.work_id = w.id
  JOIN users u ON w.user_id = u.id
  WHERE s.user_id = :uid
  ORDER BY s.created_at DESC
");
$stmt->execute(['uid' => $user_id]);
$saved = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">📚 Your Saved Works</h2>

    <?php if (empty($saved)): ?>
        <p class="text-center text-muted">You haven’t saved any works yet.</p>
    <?php else: ?>
        <div class="saved-grid">
            <?php foreach ($saved as $work): 
                $likeStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE work_id = :wid");
                $likeStmt->execute(['wid' => $work['id']]);
                $totalLikes = $likeStmt->fetchColumn();

                $whoStmt = $pdo->prepare("SELECT u.username FROM likes l JOIN users u ON l.user_id = u.id WHERE l.work_id = :wid LIMIT 3");
                $whoStmt->execute(['wid' => $work['id']]);
                $whoLiked = $whoStmt->fetchAll(PDO::FETCH_COLUMN);
            ?>
                <div class="saved-card">
                    <a href="view_work.php?id=<?= $work['id']; ?>" style="text-decoration:none; color:inherit;">
                        <div class="card-img-wrapper">
                            <img src="<?= htmlspecialchars($work['file_path']); ?>" 
                                 alt="<?= htmlspecialchars($work['title']); ?>">
                        </div>
                        <div class="card-info">
                            <h5><?= htmlspecialchars($work['title']); ?></h5>
                            <p class="text-muted mb-1">
                                <?= htmlspecialchars($work['category']); ?> · by <?= htmlspecialchars($work['username']); ?>
                            </p>
                            <?php if($totalLikes > 0): ?>
                                <p class="text-muted small mb-0">
                                    ❤️ <?= $totalLikes; ?> likes 
                                    <?php if(!empty($whoLiked)): ?>
                                        (<?= implode(', ', $whoLiked); ?>)
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.saved-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
}

.saved-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.saved-card:hover {
    transform: translateY(-4px);
}

.card-img-wrapper {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.card-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s;
}

.card-img-wrapper:hover img {
    transform: scale(1.05);
}

.card-info {
    padding: 8px 10px;
}
</style>

<?php include 'includes/footer.php'; ?>
