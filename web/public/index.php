<?php
session_start();
require_once __DIR__.'/../config/db.php';

// load last game if any
$lastGameStmt = $pdo->query("SELECT * FROM games ORDER BY created_at DESC LIMIT 1");
$lastGame = $lastGameStmt->fetch(PDO::FETCH_ASSOC);

// player count helper
$playersCount = 0;
if ($lastGame) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM game_players WHERE game_id = ?");
    $stmt->execute([$lastGame['id']]);
    $playersCount = $stmt->fetchColumn();
}

// load players for management
$players = $pdo->query("SELECT * FROM players ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Dirt Scorekeeper — Dashboard</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<header class="site-header">
  <h1>Dirt Scorekeeper</h1>
  <nav>
    <a href="new_game.php">Start New Game</a>
    <a href="stats.php">Player Stats</a>
    <a href="players.php">Manage Players</a>
    <a href="login.php">Admin</a>
  </nav>
</header>

<main class="container">
  <section class="card">
    <h2>Resume Last Game</h2>
    <?php if ($lastGame): ?>
      <p>Title: <?=htmlspecialchars($lastGame['title'])?> — Rounds: <?=$lastGame['num_rounds']?></p>
      <p>Players in last game: <?=$playersCount?></p>
      <a class="btn" href="game.php?game_id=<?=$lastGame['id']?>">Resume</a>
      <a class="btn outline" href="new_game.php?use_last=1">Start New Game with Same Players</a>
    <?php else: ?>
      <p>No games found. Start a new one.</p>
      <a class="btn" href="new_game.php">Start New Game</a>
    <?php endif; ?>
  </section>

  <section class="card">
    <h2>Quick Player List</h2>
    <?php if (count($players)): ?>
      <ul class="player-list">
      <?php foreach($players as $p): ?>
        <li><?=htmlspecialchars($p['nickname']?:$p['name'])?></li>
      <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No players yet. Go to Manage Players to add some.</p>
    <?php endif; ?>
  </section>
</main>

<footer class="site-footer">
  <small>Starter project — update config and secure admin before production.</small>
</footer>

</body>
</html>
