<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/New_York');

require_once('ORM/Game.php');

$conn = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");

if(mysqli_connect_errno()){
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

$conn->query("drop table if exists Game");
$conn->query("create table Game (" .
			 	"game_id int primary key not null auto_increment, " .
			 	"Team1 char(100), " .
			 	"Team2 char(100), " .
			 	"Team1_score int, " .
			 	"Team2_score int, " .
			 	"time date)");

$all_game_data = file_get_contents('http://www.cs.unc.edu/Courses/comp426-f15/kmp/a3/a3-data.txt');
$all_game_data = explode("\n", $all_game_data);


// insert to Player table.
$length = count($all_game_data);

$game_i =  explode(" ", $all_game_data[0]);

$team_before = [$game_i[2] => 0, $game_i[3] => 0, $game_i[4]];

for($i = 0; $i < $length; $i++){
	
	// for each game, calculate the score the winner and loser. 
	$game_i =  explode(" ", $all_game_data[$i]);
	if(count($game_i) > 1 && array_key_exists($game_i[2], $team_before) && array_key_exists($game_i[3], $team_before)){
		// the same game;
		if($game_i[5] == "fieldgoal"){
			$team_before[$game_i[2]] += 3;
		}else if($game_i[5] == "passing"){
			$team_before[$game_i[2]] += 7;
		}else if($game_i[5] == "rushing"){
			$team_before[$game_i[2]] += 7;
		}
		
	}else{
		// insert the team_before to the database, then reset the team_before
		// add the score into the team_before.
		// var_dump($team_before);
		
		$score1 = current($team_before); 
		$key1 = key($team_before);
		next($team_before);
		$score2 = current($team_before); 
		$key2 = key($team_before);
		
		if($score1 >= $score2){
			$winner = [$key1 => $score1]; $loser = [$key2 => $score2];
		}else{
			$winner = [$key2 => $score2]; $loser = [$key1 => $score1];
		}
		$result = game::insert(key($winner), key($loser), current($winner), current($loser), $team_before[0]);
		// var_dump($result);
		$result = (array)$result;
		$game_id = $result["game_id"];
		$team1 = $result["Team1"];
		$team2 = $result["Team2"];
		printf("insert Game " . "$game_id: " . "$team1". " and ". "$team2". " succeeded!");
		?>
		<br>
		<?php	
		
		// start to store the new game. Skip the last empty $game_i
		if(count($game_i) > 1){
			unset($team_before);
			$team_before = [$game_i[2] => 0, $game_i[3] => 0, $game_i[4]];
			
			if($game_i[5] == "fieldgoal"){
				$team_before[$game_i[2]] += 3;
			}else if($game_i[5] == "passing"){
				$team_before[$game_i[2]] += 7;
			}else if($game_i[5] == "rushing"){
				$team_before[$game_i[2]] += 7;
			}
		}
	}
	
}
?>

