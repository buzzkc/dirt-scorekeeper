<?php
require_once __DIR__.'/../config/db.php';

// Calculate player stats: total games played, total score across games, average final score
$players = $pdo->query('SELECT * FROM players ORDER BY nickname, name')->fetchAll(PDO::FETCH_ASSOC);

$stats = [];
foreach ($players as $p) {
    // total games played (games where player appears)
    $gp = $pdo->prepare('SELECT COUNT(DISTINCT gp.game_id) FROM game_players gp WHERE gp.player_id = ?');
    $gp->execute([$p['id']]);
    $gamesPlayed = intval($gp->fetchColumn());

    // total score across all games (sum of last round totals per game)
    $scoreStmt = $pdo->prepare('SELECT SUM(score) as total FROM rounds WHERE player_id = ?');
    $scoreStmt->execute([$p['id']]);
    $totalScore = intval($scoreStmt->fetchColumn());

    $avgScore = $gamesPlayed ? round($totalScore / $gamesPlayed,2) : 0;
    $stats[] = ['player'=>$p, 'games'=>$gamesPlayed, 'totalScore'=>$totalScore, 'avg'=>$avgScore];
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Player Stats</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<main class="container">
  <h2>Player Statistics</h2>
  <table class="stats">
    <thead><tr><th>Player</th><th>Games</th><th>Total Score</th><th>Average Score</th></tr></thead>
    <tbody>
    <?php foreach($stats as $s): ?>
      <tr>
        <td><?=htmlspecialchars($s['player']['nickname']?:$s['player']['name'])?></td>
        <td><?=$s['games']?></td>
        <td><?=$s['totalScore']?></td>
        <td><?=$s['avg']?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <a class="btn outline" href="index.php">Back</a>
</main>
</body>
</html>
