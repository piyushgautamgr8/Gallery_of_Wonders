<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gallery of Wonders</title>
  <link rel="stylesheet" href="/gallery_of_wonders/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container">
    <a href="/gallery_of_wonders/index.php" class="logo">Gallery of Wonders</a>
    <nav>
      <a href="/gallery_of_wonders/index.php">Home</a>
      <?php if(!empty($_SESSION['user'])): ?>
        <a href="/gallery_of_wonders/upload.php">Upload</a>
        <a href="/gallery_of_wonders/dashboard.php">Dashboard</a>
        <a href="/gallery_of_wonders/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['user']['username']); ?>)</a>
      <?php else: ?>
        <a href="/gallery_of_wonders/login.php">Login</a>
        <a href="/gallery_of_wonders/register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
