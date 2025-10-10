<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user']['id']) && !isset($_SESSION['user_id'])) {
  $_SESSION['user_id'] = $_SESSION['user']['id'];
}

$work_id = (int) ($_GET['id'] ?? 0);
if (!$work_id) {
  header('Location: index.php');
  exit;
}

// Fetch work
$stmt = $pdo->prepare("SELECT w.*, u.username FROM works w JOIN users u ON w.user_id = u.id WHERE w.id = :id");
$stmt->execute(['id' => $work_id]);
$work = $stmt->fetch();
if (!$work) {
  header('Location: index.php');
  exit;
}

$user_id = $_SESSION['user_id'] ?? null;

// --- LIKE SYSTEM ---
if ($user_id) {
    if (isset($_POST['like_action'])) {
        if ($_POST['like_action']==='like') {
            $pdo->prepare("INSERT IGNORE INTO likes (user_id, work_id) VALUES (:uid, :wid)")
                ->execute(['uid'=>$user_id,'wid'=>$work_id]);
        } else {
            $pdo->prepare("DELETE FROM likes WHERE user_id=:uid AND work_id=:wid")
                ->execute(['uid'=>$user_id,'wid'=>$work_id]);
        }
        header("Location: view_work.php?id=$work_id"); exit;
    }

    // --- SAVE SYSTEM ---
    if (isset($_POST['save_action'])) {
        if ($_POST['save_action']==='save') {
            $pdo->prepare("INSERT IGNORE INTO saved_works (user_id, work_id) VALUES (:uid, :wid)")
                ->execute(['uid'=>$user_id,'wid'=>$work_id]);
        } else {
            $pdo->prepare("DELETE FROM saved_works WHERE user_id=:uid AND work_id=:wid")
                ->execute(['uid'=>$user_id,'wid'=>$work_id]);
        }
        header("Location: view_work.php?id=$work_id"); exit;
    }

    // --- ADD TO COLLECTION ---
    if (isset($_POST['collection_id']) && $_POST['collection_id']!=='') {
        $cid = (int) $_POST['collection_id'];
        $check = $pdo->prepare("SELECT * FROM collections WHERE id=:cid AND user_id=:uid");
        $check->execute(['cid'=>$cid,'uid'=>$user_id]);
        if ($check->fetch()) {
            $exists = $pdo->prepare("SELECT * FROM collection_items WHERE collection_id=:cid AND work_id=:wid");
            $exists->execute(['cid'=>$cid,'wid'=>$work_id]);
            if (!$exists->fetch()) {
                $pdo->prepare("INSERT INTO collection_items (collection_id, work_id) VALUES (:cid, :wid)")
                    ->execute(['cid'=>$cid,'wid'=>$work_id]);
                $addedToCollection = true;
            } else {
                $addedToCollection = true;
            }
        }
    }

    // --- Check liked/saved status ---
    $liked = $pdo->prepare("SELECT 1 FROM likes WHERE user_id=:uid AND work_id=:wid");
    $liked->execute(['uid'=>$user_id,'wid'=>$work_id]);
    $isLiked = $liked->fetchColumn();

    $saved = $pdo->prepare("SELECT 1 FROM saved_works WHERE user_id=:uid AND work_id=:wid");
    $saved->execute(['uid'=>$user_id,'wid'=>$work_id]);
    $isSaved = $saved->fetchColumn();
}

// Count total likes
$count = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE work_id=:wid");
$count->execute(['wid'=>$work_id]);
$totalLikes = $count->fetchColumn();

// Fetch users who liked
$liked_users = $pdo->prepare("
    SELECT u.username FROM likes l 
    JOIN users u ON l.user_id = u.id 
    WHERE l.work_id = :wid ORDER BY l.id DESC
");
$liked_users->execute(['wid'=>$work_id]);
$liked_users = $liked_users->fetchAll(PDO::FETCH_COLUMN);

// Fetch user's collections
$user_collections = [];
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM collections WHERE user_id=:uid ORDER BY created_at DESC");
    $stmt->execute(['uid'=>$user_id]);
    $user_collections = $stmt->fetchAll();
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
  <!-- Work Image with Modal -->
  <div class="work-media text-center">
    <img id="fullImage" src="<?= htmlspecialchars($work['file_path']); ?>"
         alt="<?= htmlspecialchars($work['title']); ?>" class="work-image" style="cursor:pointer;">
  </div>

  <!-- Likes count (clickable) -->
  <p class="text-center text-muted mb-1" style="cursor:pointer;" id="likesCount">
    <?= $totalLikes ?> <?= $totalLikes==1?'like':'likes' ?>
  </p>

  <!-- Like + Save Buttons -->
  <?php if ($user_id): ?>
  <div class="text-center mb-3">
    <!-- Like -->
    <form method="post" style="display:inline-block;">
      <input type="hidden" name="like_action" value="<?= $isLiked ? 'unlike' : 'like'; ?>">
      <button type="submit" class="btn btn-link p-0" style="font-size:28px;">
        <?= $isLiked ? '❤️' : '🤍'; ?>
      </button>
    </form>

    <!-- Save -->
    <form method="post" style="display:inline-block; margin-left:12px;">
      <input type="hidden" name="save_action" value="<?= $isSaved ? 'unsave' : 'save'; ?>">
      <button type="submit" class="btn btn-link p-0" style="font-size:20px;">
        <?= $isSaved ? '🔖 Saved' : '📑 Save'; ?>
      </button>
    </form>
  </div>

  <!-- Add to Collection -->
  <form method="post" class="d-flex align-items-center gap-2 justify-content-center mt-3">
    <select name="collection_id" class="form-select" style="width:auto">
      <option value="">➕ Add to Collection...</option>
      <?php foreach($user_collections as $col): ?>
        <option value="<?= $col['id']; ?>"><?= htmlspecialchars($col['name']); ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">
      <?= isset($addedToCollection) && $addedToCollection ? '✅ Added' : 'Add'; ?>
    </button>
    <a href="create_collection.php" class="btn btn-outline-secondary btn-sm">New</a>
  </form>
  <?php endif; ?>

  <h2 class="mt-4"><?= htmlspecialchars($work['title']); ?></h2>
  <p class="text-muted"><?= htmlspecialchars($work['category']); ?> · by <?= htmlspecialchars($work['username']); ?></p>
  <p><?= nl2br(htmlspecialchars($work['description'] ?? '')); ?></p>
</div>

<!-- Modal HTML for Image -->
<div id="imgModal" style="display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.8); justify-content:center; align-items:center;">
  <span id="closeModal" style="position:absolute; top:20px; right:40px; font-size:30px; color:#fff; cursor:pointer;">&times;</span>
  <img id="modalImg" src="" style="max-width:90%; max-height:90%; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.4);">
</div>

<!-- Likes Modal -->
<div id="likesModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.7); justify-content:center; align-items:center;">
    <div style="background:#fff; padding:20px; border-radius:10px; max-width:400px; width:90%; max-height:70%; overflow-y:auto; text-align:left; position:relative;">
        <span id="closeLikesModal" style="position:absolute; top:10px; right:15px; font-size:24px; cursor:pointer;">&times;</span>
        <h5>Liked by:</h5>
        <ul>
            <?php if($liked_users): ?>
                <?php foreach($liked_users as $user): ?>
                    <li><?= htmlspecialchars($user); ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No likes yet</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script>
// Image Lightbox
const img = document.getElementById('fullImage');
const modal = document.getElementById('imgModal');
const modalImg = document.getElementById('modalImg');
const close = document.getElementById('closeModal');

img.onclick = function() { modal.style.display='flex'; modalImg.src=this.src; }
close.onclick = () => { modal.style.display='none'; }
modal.onclick = (e) => { if(e.target==modal) modal.style.display='none'; }

// Likes modal
const likesCount = document.getElementById('likesCount');
const likesModal = document.getElementById('likesModal');
const closeLikesModal = document.getElementById('closeLikesModal');

likesCount.onclick = () => { likesModal.style.display='flex'; }
closeLikesModal.onclick = () => { likesModal.style.display='none'; }
likesModal.onclick = (e) => { if(e.target==likesModal) likesModal.style.display='none'; }
</script>

<?php include 'includes/footer.php'; ?>
