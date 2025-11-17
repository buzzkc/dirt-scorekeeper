<?php
require_once __DIR__.'/../config/db.php';
$use_last = isset($_GET['use_last']) && $_GET['use_last'] == 1;

// load players
$players = $pdo->query('SELECT * FROM players ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);

$lastGame = null;
if ($use_last) {
    $last = $pdo->query('SELECT * FROM games ORDER BY created_at DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if ($last) {
        $lastGame = $last;
        $playerStmt = $pdo->prepare('SELECT p.* FROM players p JOIN game_players gp ON gp.player_id = p.id WHERE gp.game_id = ? ORDER BY gp.player_order');
        $playerStmt->execute([$last['id']]);
        $players = $playerStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?: 'Dirt Game';
    $num_rounds = intval($_POST['num_rounds']);
    $selected = $_POST['players'] ?? [];
    if (count($selected) < 2) {
        $error = 'Pick at least two players.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO games (title,num_rounds) VALUES (?,?)');
        $stmt->execute([$title,$num_rounds]);
        $game_id = $pdo->lastInsertId();
        $gpstmt = $pdo->prepare('INSERT INTO game_players (game_id,player_id,player_order) VALUES (?,?,?)');
        $order = 0;
        foreach ($selected as $pid) {
            $gpstmt->execute([$game_id,intval($pid), $order++]);
        }
        header('Location: game.php?game_id='.$game_id);
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Start New Game</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<main class="container">
  <h2>Start New Game</h2>
  <?php if (!empty($error)): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?>
  <form method="POST" class="card">
    <label>Title <input name="title" value="Dirt Game"></label>
    <label>Number of Rounds <input type="number" name="num_rounds" value="10" min="1" max="52"></label>

    <fieldset>
      <legend>Select Players (click to toggle)</legend>
      <?php foreach($players as $p): ?>
        <label class="player-checkbox">
          <input type="checkbox" name="players[]" value="<?=$p['id']?>"> <?=htmlspecialchars($p['nickname']?:$p['name'])?>
        </label>
      <?php endforeach; ?>
    </fieldset>

    <button class="btn" type="submit">Create Game</button>
  </form>
</main>
</body>
</html>
