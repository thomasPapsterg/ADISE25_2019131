<?php
/**
 * ===========================================================
 * WEB API FOR XERI GAME - ADISE25
 * XAMPP READY • ?path ROUTING • LIVE SCORE (NO +3)
 * ===========================================================
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit();

/* ===========================================================
   ROUTING
   =========================================================== */
$path_info = $_GET['path'] ?? '';
$method    = $_SERVER['REQUEST_METHOD'];

/* ===========================================================
   DB (XAMPP)
   =========================================================== */
$mysqli = new mysqli('127.0.0.1', 'root', '', 'iee2019131');
if ($mysqli->connect_errno) {
  echo json_encode(["status"=>"error","message"=>"DB connection failed"]);
  exit();
}
$mysqli->set_charset("utf8mb4");

/* ===========================================================
   GAME LOGIC
   =========================================================== */
require_once __DIR__ . '/game_logic.php';

/* ===========================================================
   HELPERS
   =========================================================== */
function get_bearer_token(): ?string {
  // A) Από $_SERVER (συχνά μπαίνει σε REDIRECT_HTTP_AUTHORIZATION)
  $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
  if (!empty($auth) && preg_match('/Bearer\s+(\S+)/i', $auth, $m)) return $m[1];

  // B) Από getallheaders() (fallback)
  if (function_exists('getallheaders')) {
    $h = array_change_key_case(getallheaders(), CASE_LOWER);
    if (!empty($h['authorization']) && preg_match('/Bearer\s+(\S+)/i', $h['authorization'], $m)) return $m[1];
  }

  // C) Από JSON body (fallback)
  $raw = file_get_contents('php://input');
  if ($raw) {
    $in = json_decode($raw, true);
    if (is_array($in) && !empty($in['token'])) return $in['token'];
  }

  // D) Από URL (debug)
  if (!empty($_GET['token'])) return $_GET['token'];

  return null;
}


function get_player_id(mysqli $db, string $token): ?int {
  $st = $db->prepare("SELECT player_id FROM players WHERE token=?");
  $st->bind_param("s",$token);
  $st->execute();
  $st->bind_result($pid);
  $st->fetch();
  $st->close();
  return $pid ?: null;
}

function json_body(): array {
  $raw = file_get_contents("php://input");
  $j = json_decode($raw,true);
  return is_array($j) ? $j : [];
}


/* ===========================================================
   AUTH CONTEXT
   =========================================================== */
$token = get_bearer_token();
$current_player = $token ? get_player_id($mysqli,$token) : null;

/* ===========================================================
   ENDPOINTS
   =========================================================== */

/* ---------- AUTH ---------- */
if ($path_info==='/auth' && $method==='POST'){
  $t = bin2hex(random_bytes(16));
  $st=$mysqli->prepare("INSERT INTO players(token) VALUES(?)");
  $st->bind_param("s",$t);
  $st->execute();
  echo json_encode(["status"=>"success","token"=>$t,"player_id"=>$mysqli->insert_id]);
  exit();
}

/* ---------- CREATE GAME ---------- */
if ($path_info==='/games' && $method==='POST'){
  if(!$current_player){ echo json_encode(["status"=>"error","message"=>"Unauthorized"]); exit(); }

  $state = initialize_game();
  $state['player1_id']=$current_player;
  $state['player2_id']=null;

  $st=$mysqli->prepare(
    "INSERT INTO games(player1_id,status,current_turn,board_state)
     VALUES(?, 'waiting', ?, ?)"
  );
  $json=json_encode($state,JSON_UNESCAPED_UNICODE);
  $st->bind_param("iis",$current_player,$current_player,$json);
  $st->execute();

  echo json_encode(["status"=>"waiting","game_id"=>$mysqli->insert_id,"board_state"=>$state]);
  exit();
}

/* ---------- JOIN GAME ---------- */
if (preg_match('#^/games/(\d+)/join$#',$path_info,$m) && $method==='POST'){
  if(!$current_player){ echo json_encode(["status"=>"error","message"=>"Unauthorized"]); exit(); }
  $gid=(int)$m[1];

  $g=$mysqli->query("SELECT * FROM games WHERE game_id=$gid")->fetch_assoc();
  if(!$g || $g['status']!=='waiting'){ echo json_encode(["status"=>"error"]); exit(); }

  $board=json_decode($g['board_state'],true);
  $board['player2_id']=$current_player;

  $st=$mysqli->prepare(
    "UPDATE games SET player2_id=?,status='active',board_state=? WHERE game_id=?"
  );
  $json=json_encode($board,JSON_UNESCAPED_UNICODE);
  $st->bind_param("isi",$current_player,$json,$gid);
  $st->execute();

  echo json_encode(["status"=>"active"]);
  exit();
}

/* ---------- VIEW GAME ---------- */
if (preg_match('#^/games/(\d+)$#',$path_info,$m) && $method==='GET'){
  $gid=(int)$m[1];
  $g=$mysqli->query("SELECT * FROM games WHERE game_id=$gid")->fetch_assoc();
  if(!$g){ echo json_encode(["status"=>"error"]); exit(); }

  $board=json_decode($g['board_state'],true);

  echo json_encode([
    "status"=>$g['status'],
    "current_turn"=>(int)$g['current_turn'],
    "board_state"=>$board,
    "player1_id"=>(int)$g['player1_id'],
    "player2_id"=>$g['player2_id']? (int)$g['player2_id']:null,
    "live_scores"=>calculate_live_score($board)
  ],JSON_UNESCAPED_UNICODE);
  exit();
}

/* ---------- MOVE ---------- */
if (preg_match('#^/games/(\d+)/move$#',$path_info,$m) && $method==='POST'){
  if(!$current_player){ echo json_encode(["status"=>"error","message"=>"Unauthorized"]); exit(); }

  $gid=(int)$m[1];
  $in=json_body();

  $g=$mysqli->query("SELECT * FROM games WHERE game_id=$gid")->fetch_assoc();
  if(!$g || (int)$g['current_turn']!==$current_player){
    echo json_encode(["status"=>"error","message"=>"Not your turn"]); exit();
  }

  $board=json_decode($g['board_state'],true);
  $board['player1_id']=(int)$g['player1_id'];
  $board['player2_id']=$g['player2_id']? (int)$g['player2_id']:null;

  $game_data=[
    "player1_id"=>$board['player1_id'],
    "player2_id"=>$board['player2_id'],
    "board_state"=>$board
  ];

  $res=apply_move_and_check_rules(
    $game_data,
    $current_player,
    $in['player_card'],
    $in['table_cards'] ?? []
  );
  if(!empty($res['error'])){ echo json_encode(["status"=>"error","message"=>$res['error']]); exit(); }

  $opp = ($current_player===$board['player1_id']) ? $board['player2_id'] : $board['player1_id'];

  $round=check_end_of_round_or_game($game_data,$res['board_state'],$current_player,$opp);

  $final_scores = ($round['status']==='ended')
    ? calculate_final_score($game_data,$round['board_state'])
    : null;

  $json=json_encode($round['board_state'],JSON_UNESCAPED_UNICODE);
  $st=$mysqli->prepare("UPDATE games SET board_state=?,current_turn=?,status=? WHERE game_id=?");
  $st->bind_param("sisi",$json,$opp,$round['status'],$gid);
  $st->execute();

  echo json_encode([
    "status"=>$round['status'],
    "current_turn"=>$opp,
    "board_state"=>$round['board_state'],
    "live_scores"=>calculate_live_score($round['board_state']),
    "final_scores"=>$final_scores
  ],JSON_UNESCAPED_UNICODE);
  exit();
}

/* ---------- DEFAULT ---------- */
echo json_encode(["status"=>"online","info"=>"Xeri API OK"]);

