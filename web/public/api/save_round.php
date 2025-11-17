<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../score.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error'=>'POST only']);
    exit;
}

$game_id = intval($_POST['game_id'] ?? 0);
$round_number = intval($_POST['round_number'] ?? 0);
if (!$game_id || !$round_number) {
    http_response_code(400);
    echo json_encode(['error'=>'missing params']);
    exit;
}

// get players for game
$players = $pdo->prepare('SELECT player_id FROM game_players WHERE game_id = ? ORDER BY player_order');
$players->execute([$game_id]);
$pids = $players->fetchAll(PDO::FETCH_COLUMN);

$allMade = true; $noneMade = true;
$ins = $pdo->prepare('INSERT INTO rounds (game_id,round_number,player_id,bid,hands_won,score,all_players_made_bid,no_players_made_bid) VALUES (?,?,?,?,?,?,?,?)');

foreach ($pids as $pid) {
    $bid = intval($_POST['bid_'.$pid] ?? 0);
    $won = intval($_POST['won_'.$pid] ?? 0);
    $score = calculate_score($bid,$won);

    if ($bid != $won) $allMade = false;
    if ($won > 0) $noneMade = false;
    $ins->execute([$game_id,$round_number,$pid,$bid,$won,$score,false,false]);
}

// Update flags for this round (all rows)
$upd = $pdo->prepare('UPDATE rounds SET all_players_made_bid = ?, no_players_made_bid = ? WHERE game_id = ? AND round_number = ?');
$upd->execute([$allMade,$noneMade,$game_id,$round_number]);

echo json_encode(['success'=>true,'all_made_bid'=>$allMade,'none_made_bid'=>$noneMade]);
