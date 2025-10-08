<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php'); exit;
}
// increment view
$pdo->prepare("UPDATE works SET views = views + 1 WHERE id = :id")->execute(['id'=>$id]);

$stmt = $pdo->prepare("SELECT w.*, u.username FROM works w JOIN users u ON w.user_id = u.id WHERE w.id = :id");
$stmt->execute(['id'=>$id]);
$w = $stmt->fetch();
if (!$w) { header('Location: index.php'); exit; }
?>
<?php include 'includes/header.php'; ?>
<article class="work-detail">
  <h2><?php echo htmlspecialchars($w['title']); ?></h2>
  <p class="meta">by <?php echo htmlspecialchars($w['username']); ?> — <?php echo htmlspecialchars($w['category']); ?></p>
  <div class="work-media">
    <img src="assets/uploads/<?php echo htmlspecialchars($w['file_path']); ?>" alt="<?php echo htmlspecialchars($w['title']); ?>">
  </div>
  <div class="work-desc">
    <p><?php echo nl2br(htmlspecialchars($w['description'])); ?></p>
  </div>
  <p class="meta">Views: <?php echo $w['views'] + 1; ?></p>
</article>
<?php include 'includes/footer.php'; ?>
