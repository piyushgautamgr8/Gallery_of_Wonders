<?php
require 'includes/db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$username) $errors[] = "Username required";
    if (!$email) $errors[] = "Valid email required";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 chars";
    if ($password !== $password2) $errors[] = "Passwords do not match";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e");
        $stmt->execute(['u'=>$username,'e'=>$email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already taken";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (username,email,password) VALUES (:u,:e,:p)");
            $ins->execute(['u'=>$username,'e'=>$email,'p'=>$hash]);
            $_SESSION['user'] = ['id'=>$pdo->lastInsertId(), 'username'=>$username, 'email'=>$email];
            header('Location: index.php'); exit;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<h2>Register</h2>
<?php if($errors): foreach($errors as $e): ?>
  <div class="alert"><?php echo htmlspecialchars($e); ?></div>
<?php endforeach; endif; ?>
<form method="post" class="form">
  <label>Username <input name="username" required></label>
  <label>Email <input name="email" type="email" required></label>
  <label>Password <input name="password" type="password" required></label>
  <label>Confirm Password <input name="password2" type="password" required></label>
  <button type="submit">Register</button>
</form>
<?php include 'includes/footer.php'; ?>
