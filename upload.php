<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE)
    session_start();
if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if (!$title)
        $errors[] = "Title required";

    if (empty($_FILES['file']['name']))
        $errors[] = "Please upload a file";

    if (empty($errors)) {
        $file = $_FILES['file'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain'];
        if (!in_array($file['type'], $allowed)) {
            $errors[] = "File type not supported";
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $errors[] = "File too large (max 5MB)";
        } else {
            $targetDir = __DIR__ . '/assets/uploads/';
            if (!is_dir($targetDir))
                mkdir($targetDir, 0755, true);
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fname = uniqid('w_') . '.' . $ext;
            $dest = $targetDir . $fname;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $insert = $pdo->prepare("INSERT INTO works (user_id,title,description,category,file_path) VALUES (:uid,:t,:d,:c,:f)");
                $insert->execute([
                    'uid' => $_SESSION['user']['id'],
                    't' => $title,
                    'd' => $description,
                    'c' => $category,
                    'f' => 'assets/uploads/' . $fname
                ]);
                header('Location: dashboard.php');
                exit;
            } else {
                $errors[] = "Upload failed";
            }
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<h2>Upload Work</h2>

<?php if ($errors): ?>
  <?php foreach ($errors as $e): ?>
    <div class="alert"><?php echo htmlspecialchars($e); ?></div>
  <?php endforeach; ?>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="form">
  <label>Title <input name="title" required></label>

  <label>Category</label>
  <select name="category" id="categorySelect" onchange="toggleCustomCategory()">
    <option value="">Select category</option>
    <option value="Nature">Nature</option>
    <option value="Technology">Technology</option>
    <option value="Movies">Movies</option>
    <option value="Flowers">Flowers</option>
    <option value="Superhero">Superhero</option>
    <option value="custom">Custom (enter your own)</option>
  </select>

  <input type="text" id="customCategory" name="category" placeholder="Enter custom category" style="display:none; margin-top:8px;">

  <label>Description <textarea name="description"></textarea></label>
  <label>File (image/pdf/text) <input type="file" name="file" required></label>
  <button type="submit">Upload</button>
</form>

<script>
function toggleCustomCategory() {
  const select = document.getElementById('categorySelect');
  const customInput = document.getElementById('customCategory');
  if (select.value === 'custom') {
    customInput.style.display = 'block';
    customInput.name = 'category';
    select.removeAttribute('name');
  } else {
    customInput.style.display = 'none';
    select.name = 'category';
    customInput.removeAttribute('name');
  }
}
</script>

<?php include 'includes/footer.php'; ?>
