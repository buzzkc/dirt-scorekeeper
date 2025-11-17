<?php
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/score.php';


$game_id = isset($_GET['game_id']) ? intval($_GET['game_id']) : 0;
if (!$game_id) { header('Location: index.php'); exit; }

// load game
$game = $pdo->prepare('SELECT * FROM games WHERE id = ?');
$game->execute([$game_id]);
$game = $game->fetch(PDO::FETCH_ASSOC);
if (!$game) { die('Game not found'); }

// load players (ordered)
$players = $pdo->prepare('SELECT p.* FROM players p JOIN game_players gp ON gp.player_id = p.id WHERE gp.game_id = ? ORDER BY gp.player_order');
$players->execute([$game_id]);
$players = $players->fetchAll(PDO::FETCH_ASSOC);

// load existing rounds aggregated by round_number
$roundsStmt = $pdo->prepare('SELECT * FROM rounds WHERE game_id = ? ORDER BY round_number, id');
$roundsStmt->execute([$game_id]);
$roundRows = $roundsStmt->fetchAll(PDO::FETCH_ASSOC);

// build a map of round_number => rows per player
$roundsMap = [];
foreach ($roundRows as $r) {
    $roundsMap[$r['round_number']][] = $r;
}

// Fetch total score per player
$totalsStmt = $pdo->prepare("
    SELECT player_id, SUM(score) as total_score
    FROM rounds
    WHERE game_id = ?
    GROUP BY player_id
");
$totalsStmt->execute([$game_id]);
$totalsRaw = $totalsStmt->fetchAll(PDO::FETCH_ASSOC);

// Format totals into associative array
$totals = [];
foreach ($totalsRaw as $row) {
    $totals[$row['player_id']] = $row['total_score'];
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Game <?=htmlspecialchars($game['title'])?></title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<main class="container">
  <h2><?=htmlspecialchars($game['title'])?></h2>
  <p>Rounds: <?=$game['num_rounds']?></p>

  <?php for ($r=1;$r<=$game['num_rounds'];$r++): 
      $entries = $roundsMap[$r] ?? [];
      $hasSaved = count($entries) > 0;
  ?>
    <section class="card">
      <h3>Round <?=$r?> — Cards: <?=($game['num_rounds'] - $r + 1)?></h3>

      <?php if ($hasSaved): ?>
        <p>Saved results exist for this round. <a href="edit_round.php?game_id=<?=$game_id?>&round=<?=$r?>">Edit Round</a></p>
        <table class="scores">
          <thead><tr><th>Player</th><th>Bid</th><th>Won</th><th>Score</th></tr></thead>
          <tbody>
          <?php foreach($entries as $e): ?>
            <?php $pstmt = $pdo->prepare('SELECT * FROM players WHERE id=?'); $pstmt->execute([$e['player_id']]); $pp = $pstmt->fetch(PDO::FETCH_ASSOC); ?>
            <tr>
              <td><?=htmlspecialchars($pp['nickname']?:$pp['name'])?></td>
              <td><?=$e['bid']?></td>
              <td><?=$e['hands_won']?></td>
              <td><?=$e['score']?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <form class="card" method="POST" action="api/save_round.php">
          <input type="hidden" name="game_id" value="<?=$game_id?>">
          <input type="hidden" name="round_number" value="<?=$r?>">
          <div class="grid">
            <?php foreach($players as $p): ?>
              <div class="player-block">
                <strong><?=htmlspecialchars($p['nickname']?:$p['name'])?></strong>
                <label>Bid <input type="number" name="bid_<?=$p['id']?>" min="0" max="<?=($game['num_rounds'] - $r + 1)?>" value="0"></label>
                <label>Won <input type="number" name="won_<?=$p['id']?>" min="0" max="<?=($game['num_rounds'] - $r + 1)?>" value="0"></label>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="actions">
            <button class="btn" type="submit">Save Round</button>
          </div>
        </form>
      <?php endif; ?>
    </section>
  <?php endfor; ?>
	<section class="summary-card">
		<h2>Player Totals</h2>

		<div class="totals-grid">
			<?php foreach ($players as $p): ?>
				<div class="total-box">
					<div class="player-name"><?= htmlspecialchars($p['name']) ?></div>
					<div class="player-total">
						<?= $totals[$p['id']] ?? 0 ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
  <a class="btn outline" href="index.php">Back to Dashboard</a>
</main>
<script src="js/app.js"></script>
</body>
</html>
