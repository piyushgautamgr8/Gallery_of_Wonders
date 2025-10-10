<?php
session_start();
require 'includes/db_connect.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user works
$stmt = $pdo->prepare("SELECT * FROM works WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute(['uid' => $user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
  <h2 class="text-center mb-4">My Uploaded Works</h2>
  <div class="text-end mb-3">
    <a href="upload.php" class="btn btn-primary">+ Upload New Work</a>
  </div>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Preview</th>
        <th>Title</th>
        <th>Category</th>
        <th>Uploaded On</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($result as $row): ?>
        <tr>
          <td>
            <?php if (!empty($row['file_path'])): ?>
              <img src="<?= htmlspecialchars($row['file_path']); ?>"
                   alt="Work Image"
                   class="preview-img"
                   style="width:80px; height:80px; object-fit:cover; border-radius:8px; cursor:pointer;">
            <?php else: ?>
              <span>No image</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($row['title']); ?></td>
          <td><?= htmlspecialchars($row['category']); ?></td>
          <td><?= htmlspecialchars($row['created_at']); ?></td>
          <td>
            <a href="edit_work.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_work.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
               onclick="return confirm('Delete this work?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- 🔹 Lightbox for full image preview -->
<div id="lightbox" class="lightbox">
  <span class="close">&times;</span>
  <img class="lightbox-content" id="lightboxImg">
</div>

<?php include 'includes/footer.php'; ?>

<!-- 🔹 Lightbox Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const previewImgs = document.querySelectorAll(".preview-img");
  const lightbox = document.getElementById("lightbox");
  const lightboxImg = document.getElementById("lightboxImg");
  const closeBtn = document.querySelector(".lightbox .close");

  if (previewImgs.length && lightbox && lightboxImg && closeBtn) {
    previewImgs.forEach(img => {
      img.addEventListener("click", () => {
        lightboxImg.src = img.src;
        lightbox.classList.add("show");
        lightbox.style.display = "flex";
      });
    });

    function closeLightbox() {
      lightbox.classList.remove("show");
      setTimeout(() => (lightbox.style.display = "none"), 300);
    }

    closeBtn.addEventListener("click", closeLightbox);
    lightbox.addEventListener("click", (e) => {
      if (e.target === lightbox) closeLightbox();
    });
  }
});
</script>

<!-- 🔹 Lightbox CSS -->
<style>
.lightbox {
  display: none;
  position: fixed;
  z-index: 9999;
  inset: 0;
  background: rgba(0, 0, 0, 0.85);
  justify-content: center;
  align-items: center;
  animation: fadeIn 0.3s ease;
}
.lightbox.show {
  display: flex;
}
.lightbox-content {
  max-width: 80%;
  max-height: 80%;
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(255,255,255,0.2);
  animation: zoomIn 0.3s ease;
}
.lightbox .close {
  position: absolute;
  top: 20px;
  right: 30px;
  font-size: 40px;
  color: #fff;
  cursor: pointer;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
@keyframes zoomIn {
  from { transform: scale(0.8); }
  to { transform: scale(1); }
}
</style>
