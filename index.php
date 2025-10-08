<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// fetch latest works
$stmt = $pdo->query("SELECT w.*, u.username FROM works w JOIN users u ON w.user_id = u.id ORDER BY w.upload_date DESC LIMIT 8");
$works = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

<section class="hero">
  <h1>Gallery of Wonders</h1>
  <p>Preserving and showcasing creative works & performances.</p>
  <div class="hero-actions">
    <a class="btn" href="upload.php">Upload</a>
    <?php if(empty($_SESSION['user'])): ?>
      <a class="btn hollow" href="login.php">Login / Register</a>
    <?php else: ?>
      <a class="btn hollow" href="dashboard.php">My Dashboard</a>
    <?php endif; ?>
  </div>
</section>

<h2>Featured Works</h2>
<section class="grid">
  <?php if(empty($works)): ?>
    <p>No works yet. Be the first to upload!</p>
  <?php else: foreach($works as $w): ?>
    <article class="card">
      <a href="view_work.php?id=<?php echo $w['id']; ?>">
        <img src="assets/uploads/<?php echo htmlspecialchars(basename($w['file_path'])); ?>" alt="<?php echo htmlspecialchars($w['title']); ?>">
      </a>
      <h3><?php echo htmlspecialchars($w['title']); ?></h3>
      <p class="meta">by <?php echo htmlspecialchars($w['username']); ?></p>
    </article>
  <?php endforeach; endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
