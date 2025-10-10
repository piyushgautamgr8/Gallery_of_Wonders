<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$user_id = $_SESSION['user_id'] ?? 0;

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

$query = "SELECT w.*, u.username,
          (SELECT COUNT(*) FROM likes l WHERE l.work_id=w.id) AS like_count,
          (SELECT COUNT(*) FROM saved_works s WHERE s.work_id=w.id AND s.user_id=:uid) AS is_saved
          FROM works w
          JOIN users u ON w.user_id=u.id
          WHERE 1";
$params = ['uid'=>$user_id];

if($search) {
  $query .= " AND (w.title LIKE :search OR u.username LIKE :search OR w.description LIKE :search)";
  $params['search'] = "%$search%";
}
if($category) {
  $query .= " AND w.category=:category";
  $params['category'] = $category;
}

$query .= " ORDER BY w.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$works = $stmt->fetchAll();

if(empty($works)) { echo '<p class="text-center text-muted">No results found.</p>'; exit; }

foreach($works as $w): ?>
<div class="explore-item">
  <a href="view_work.php?id=<?= $w['id']; ?>">
    <img src="<?= htmlspecialchars($w['file_path']); ?>" alt="<?= htmlspecialchars($w['title']); ?>">
  </a>
  <?php if($user_id): ?>
    <span class="bookmark-btn <?= $w['is_saved']?'saved':'';?>" data-id="<?= $w['id']; ?>">
      <?= $w['is_saved']?'🔖':'📑'; ?>
    </span>
  <?php endif; ?>
  <div class="overlay">
    <h6><?= htmlspecialchars($w['title']); ?></h6>
    <p>@<?= htmlspecialchars($w['username']); ?> · ❤️ <?= $w['like_count']; ?></p>
  </div>
</div>
<?php endforeach; ?>
