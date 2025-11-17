<?php
session_start();
$admin = require __DIR__.'/../config/admin.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    if ($u === $admin['username'] && $p === $admin['password']) {
        $_SESSION['admin'] = true;
        header('Location: players.php');
        exit;
    } else {
        $err = 'Invalid credentials';
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<main class="container">
  <h2>Admin Login</h2>
  <?php if ($err): ?><p class="error"><?=htmlspecialchars($err)?></p><?php endif; ?>
  <form method="POST">
    <label>Username
      <input name="username" required>
    </label>
    <label>Password
      <input name="password" type="password" required>
    </label>
    <button class="btn" type="submit">Login</button>
  </form>
</main>
</body>
</html>
