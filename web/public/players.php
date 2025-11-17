<?php
session_start();
$admin = require __DIR__.'/../config/admin.php';
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__.'/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = $_POST['name'];
    $nick = $_POST['nickname'] ?? '';
    $stmt = $pdo->prepare("INSERT INTO players (name,nickname) VALUES (?,?)");
    $stmt->execute([$name,$nick]);
    header('Location: players.php');
    exit;
}

$players = $pdo->query('SELECT * FROM players ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Players</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<main class="container">
  <h2>Players</h2>
  <form method="POST" class="card">
    <label>Name <input name="name" required></label>
    <label>Nickname <input name="nickname"></label>
    <button class="btn" type="submit">Add Player</button>
  </form>

  <section class="card">
    <h3>Existing Players</h3>
    <ul>
    <?php foreach($players as $p): ?>
      <li><?=htmlspecialchars($p['nickname']?:$p['name'])?> — <small><?=htmlspecialchars($p['name'])?></small></li>
    <?php endforeach; ?>
    </ul>
  </section>
</main>
</body>
</html>
