<?php
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/score.php';

$game_id = intval($_GET['game_id'] ?? 0);
$round = intval($_GET['round'] ?? 0);
if (!$game_id || !$round) { header('Location: index.php'); exit; }

// load players in this game
$players = $pdo->prepare('SELECT p.* FROM players p JOIN game_players gp ON gp.player_id = p.id WHERE gp.game_id = ? ORDER BY gp.player_order');
$players->execute([$game_id]);
$players = $players->fetchAll(PDO::FETCH_ASSOC);

// handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // simple approach: delete existing rows for this round then insert new
    $pdo->prepare('DELETE FROM rounds WHERE game_id = ? AND round_number = ?')->execute([$game_id,$round]);
    $allMade = true; $noneMade = true;
    $ins = $pdo->prepare('INSERT INTO rounds (game_id,round_number,player_id,bid,hands_won,score,all_players_made_bid,no_players_made_bid) VALUES (?,?,?,?,?,?,?,?)');
    foreach ($players as $p) {
        $pid = $p['id'];
        $bid = intval($_POST['bid_'.$pid] ?? 0);
        $won = intval($_POST['won_'.$pid] ?? 0);
        $score = calculate_score($bid,$won);
        if ($bid != $won) $allMade = false;
        if ($won > 0) $noneMade = false;
        $ins->execute([$game_id,$round,$pid,$bid,$won,$score,$allMade,$noneMade]);
    }
    header('Location: game.php?game_id='.$game_id);
    exit;
}

// load existing row values if present
$existing = [];
$stmt = $pdo->prepare('SELECT * FROM rounds WHERE game_id = ? AND round_number = ?');
$stmt->execute([$game_id,$round]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) { $existing[$r['player_id']] = $r; }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Round <?=$round?></title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<main class="container">
  <h2>Edit Round <?=$round?></h2>
  <form method="POST" class="card">
    <div class="grid">
      <?php foreach($players as $p): 
        $e = $existing[$p['id']] ?? null;
      ?>
        <div class="player-block">
          <strong><?=htmlspecialchars($p['nickname']?:$p['name'])?></strong>
          <label>Bid <input type="number" name="bid_<?=$p['id']?>" min="0" max="<?=($game['num_rounds'] - $round + 1)?>" value="<?= $e ? $e['bid'] : 0 ?>"></label>
          <label>Won <input type="number" name="won_<?=$p['id']?>" min="0" max="<?=($game['num_rounds'] - $round + 1)?>" value="<?= $e ? $e['hands_won'] : 0 ?>"></label>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="actions">
      <button class="btn" type="submit">Save Round</button>
      <a class="btn outline" href="game.php?game_id=<?=$game_id?>">Cancel</a>
    </div>
  </form>
</main>
</body>
</html>
