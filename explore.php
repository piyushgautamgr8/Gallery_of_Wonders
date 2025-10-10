<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$user_id = $_SESSION['user']['id'] ?? 0;
$search = trim($_GET['q'] ?? '');

// Fetch works with optional search
$sql = "SELECT w.*, u.username
        FROM works w
        JOIN users u ON w.user_id = u.id
        WHERE 1";

$params = [];
if ($search) {
    $sql .= " AND (w.title LIKE :s OR w.category LIKE :s OR u.username LIKE :s)";
    $params['s'] = "%$search%";
}

$sql .= " ORDER BY w.upload_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$works = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Explore Works</h2>
    <div class="mb-3">
        <input type="text" id="searchInput" placeholder="Search works..." class="form-control" value="<?= htmlspecialchars($search) ?>">
    </div>

    <div id="worksGrid" class="grid">
        <?php foreach ($works as $w): ?>
            <div class="card explore-card">
                <a href="view_work.php?id=<?= $w['id']; ?>" class="img-link">
                    <img src="<?= htmlspecialchars($w['file_path']); ?>" alt="<?= htmlspecialchars($w['title']); ?>" class="card-img-top">
                    <div class="overlay">
                        <button class="bookmark-btn btn btn-sm btn-outline-light" data-work="<?= $w['id']; ?>">
                            💾
                        </button>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
const searchInput = document.getElementById('searchInput');
const worksGrid = document.getElementById('worksGrid');

// AJAX search
searchInput.addEventListener('input', function() {
    const query = this.value;
    fetch(`explore.php?q=${encodeURIComponent(query)}`, { headers: { 'X-Requested-With':'XMLHttpRequest' } })
    .then(res => res.text())
    .then(html => {
        // parse returned HTML and update grid
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newGrid = doc.getElementById('worksGrid');
        if(newGrid) worksGrid.innerHTML = newGrid.innerHTML;
        attachBookmarkListeners();
    });
});

// Bookmark/save button
function attachBookmarkListeners() {
    document.querySelectorAll('.bookmark-btn').forEach(btn => {
        btn.onclick = function(e) {
            e.preventDefault();
            const workId = this.dataset.work;
            fetch('bookmark_work.php', {
                method: 'POST',
                headers: { 'Content-Type':'application/x-www-form-urlencoded' },
                body: `work_id=${workId}`
            }).then(res => res.json())
              .then(data => {
                  if(data.success) {
                      this.innerText = '✔';
                  } else {
                      alert('Error saving work');
                  }
              });
        }
    });
}

// Initial attach
attachBookmarkListeners();
</script>

<style>
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px,1fr));
    gap: 12px;
}
.explore-card {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}
.explore-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    display: block;
    transition: transform 0.3s;
}
.explore-card:hover img {
    transform: scale(1.05);
}
.overlay {
    position: absolute;
    top: 6px;
    right: 6px;
    pointer-events: none; /* let clicks pass through to link */
}
.bookmark-btn {
    pointer-events: auto;
}
</style>

<?php include 'includes/footer.php'; ?>
