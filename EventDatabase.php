<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/New_York');

require_once('ORM/Event.php');

$conn = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");

if(mysqli_connect_errno()){
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

$conn->query("drop table if exists Event");
$conn->query("create table Event (" .
			 	"event_id int not null auto_increment, " .
			 	"p_id int not null, " .
			 	"g_id int not null, " .
			 	"rushing int, " .
			 	"passing int, " .
			 	"fieldgoal int, " .
			 	"passTo int, " .
			 	"PRIMARY KEY(event_id), " .
			 	"FOREIGN KEY(p_id) REFERENCES Player(player_id), " .
			 	"FOREIGN KEY(g_id) REFERENCES Game(game_id))");

$all_game_data = file_get_contents('http://www.cs.unc.edu/Courses/comp426-f15/kmp/a3/a3-data.txt');
$all_game_data = explode("\n", $all_game_data);


// insert to Player table.
$length = count($all_game_data);

for($i = 0; $i < $length - 1; $i++){
	$game_i =  explode(" ", $all_game_data[$i]);

	$fn = $game_i[0];
	$ln = $game_i[1];
	$tn = $game_i[2];
	$player_id = $conn->query("select player_id from Player " .
								"where first_name = '$fn' && last_name = '$ln' && team_name = '$tn'");
	if($player_id->num_rows == 0){
		printf("Error: No player found!");
		exit();
	} 
	$player_id = $player_id->fetch_row()[0];
	
	$tn1 = $game_i[2];
	$tn2 = $game_i[3];
	$game_time = $game_i[4];
	
	$game_id = $conn->query("select game_id from Game " . 
							  "where (Team1 = '$tn1' AND Team1 = '$tn1' OR " .
							  		"Team2 = '$tn1' AND Team1 = '$tn2') AND time = '$game_time'");
	if($game_id->num_rows == 0){
		printf("Error: No Game found!");
		exit();
	}
	$game_id = $game_id->fetch_row()[0];
	
	$cnt = ["rushing" => 0, "passing" => 0, "fieldgoal" => 0, "passTo" => NULL];
	$cnt[$game_i[5]] ++;
	if($game_i[5] == "passing"){
		$fn = $game_i[6];
		$ln = $game_i[7];
		$tn = $game_i[2];
		$passToPlayer = $conn->query("select player_id from Player where first_name = '$fn' && last_name = '$ln' && team_name = '$tn' ");
		if($passToPlayer->num_rows == 0){
			printf("Error: No passingTO player found!");
			exit();
		} 
		$cnt["passTo"] = $passToPlayer->fetch_row()[0];
	}
	
	$exist = $conn->query("select * from Event where p_id = '$player_id' AND g_id = '$game_id'");
	if($exist->num_rows == 0){
		// this is a new Event, insert to the Event table.
		// rushing touch down should be initialized. 
		$result = event::insert($player_id, $game_id, $cnt["rushing"], $cnt["passing"], $cnt["fieldgoal"], $cnt["passTo"]);
	}else{
		// the event is already inserted. 
		// update the table.
		$result = event::update($player_id, $game_id, $cnt["rushing"], $cnt["passing"], $cnt["fieldgoal"], $cnt["passTo"]);
	}
}
?>

