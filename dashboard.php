<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user'])) {
    header('Location: login.php'); exit;
}

$uid = $_SESSION['user']['id'];

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // ensure ownership
    $s = $pdo->prepare("SELECT file_path FROM works WHERE id = :id AND user_id = :uid");
    $s->execute(['id'=>$id,'uid'=>$uid]);
    $row = $s->fetch();
    if ($row) {
        @unlink(__DIR__.'/assets/uploads/'.$row['file_path']);
        $d = $pdo->prepare("DELETE FROM works WHERE id = :id");
        $d->execute(['id'=>$id]);
    }
    header('Location: dashboard.php'); exit;
}

$stmt = $pdo->prepare("SELECT * FROM works WHERE user_id = :uid ORDER BY upload_date DESC");
$stmt->execute(['uid'=>$uid]);
$works = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>
<h2>My Dashboard</h2>
<p><a href="upload.php" class="btn">Upload new work</a></p>

<?php if(empty($works)): ?>
  <p>No uploads yet.</p>
<?php else: ?>
  <div class="grid">
    <?php foreach($works as $w): ?>
      <article class="card">
        <a href="view_work.php?id=<?php echo $w['id']; ?>">
          <img src="assets/uploads/<?php echo htmlspecialchars($w['file_path']); ?>" alt="">
        </a>
        <h3><?php echo htmlspecialchars($w['title']); ?></h3>
        <p class="meta"><?php echo htmlspecialchars($w['upload_date']); ?></p>
        <p>
          <a class="btn small" href="view_work.php?id=<?php echo $w['id']; ?>">View</a>
          <a class="btn small danger" href="dashboard.php?delete=<?php echo $w['id']; ?>" onclick="return confirm('Delete this work?')">Delete</a>
        </p>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
