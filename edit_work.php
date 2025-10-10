<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$work_id = $_GET['id'] ?? null;

if (!$work_id) {
    die("Invalid work ID");
}

// Fetch existing work data (ensure ownership)
$stmt = $pdo->prepare("SELECT * FROM works WHERE id = :id AND user_id = :uid");
$stmt->execute(['id' => $work_id, 'uid' => $user_id]);
$work = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$work) {
    die("Work not found or you don’t have permission to edit it.");
}

$success = "";
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $file_path = $work['file_path']; // keep old file by default

    // If new file uploaded, replace
    if (!empty($_FILES['file']['name'])) {
        $target_dir = "assets/uploads/";
        $filename = time() . "_" . basename($_FILES["file"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            // Delete old file if exists
            if (file_exists($work['file_path'])) {
                unlink($work['file_path']);
            }
            $file_path = $target_file;
        } else {
            $errors[] = "Error uploading file.";
        }
    }


    if (empty($errors)) {
        $update = $pdo->prepare("UPDATE works 
                                 SET title = :t, description = :d, category = :c, file_path = :f 
                                 WHERE id = :id AND user_id = :uid");
        $update->execute([
            't' => $title,
            'd' => $description,
            'c' => $category,
            'f' => $file_path,
            'id' => $work_id,
            'uid' => $user_id
        ]);

        $success = "Work updated successfully!";
        // Refresh updated info
        $stmt->execute(['id' => $work_id, 'uid' => $user_id]);
        $work = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5" style="max-width:700px;">
    <h2 class="text-center mb-4">Edit Work</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data" class="form">
        <label>Title
            <input type="text" name="title" value="<?= htmlspecialchars($work['title']); ?>" required>
        </label>

        <label>Description
            <textarea name="description" rows="4"><?= htmlspecialchars($work['description']); ?></textarea>
        </label>

        <label>Category
            <input type="text" name="category" value="<?= htmlspecialchars($work['category']); ?>" required>
        </label>

        <p>Current File:</p>
        <img src="<?= htmlspecialchars($work['file_path']); ?>" style="max-width:200px;border:1px solid #ccc;"><br><br>

        <label>Replace File (optional)
            <input type="file" name="file">
        </label>

        <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>