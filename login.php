<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['user'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (!$user || !$pass) $errors[] = "Fill both fields";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u OR email = :u");
        $stmt->execute(['u'=>$user]);
        $found = $stmt->fetch();
        if ($found && password_verify($pass, $found['password'])) {
            unset($found['password']);
            $_SESSION['user'] = $found;
            $_SESSION['user_id'] = $found['id'];
            header('Location: index.php'); exit;
        } else {
            $errors[] = "Invalid credentials";
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<h2>Login</h2>
<?php if($errors): foreach($errors as $e): ?>
  <div class="alert"><?php echo htmlspecialchars($e); ?></div>
<?php endforeach; endif; ?>
<form method="post" class="form">
  <label>Username or Email <input name="user" required></label>
  <label>Password <input name="password" type="password" required></label>
  <button type="submit">Login</button>
</form>
<?php include 'includes/footer.php'; ?>
