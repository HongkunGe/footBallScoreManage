<?php
	
	
class game{
	public $game_id;
	public $Team1;
	public $Team2;
	public $Team1_score;
	public $Team2_score;
	public $game_time;
	
	private function __construct($game_id, $Team1, $Team2, $Team1_score, $Team2_score, $game_time ){
		$this->game_id = $game_id;
		$this->Team1 = $Team1;
		$this->Team2 = $Team2;
		$this->Team1_score = $Team1_score;
		$this->Team2_score = $Team2_score;
		$this->game_time = $game_time;
	}
	
	public static function insert($Team1, $Team2, $Team1_score, $Team2_score, $game_time ){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		
		// $player_id = $mysqli->real_escape_string($player_id);
		$Team1 = $mysqli->real_escape_string($Team1);
		$Team2 = $mysqli->real_escape_string($Team2);
		$Team1_score = $mysqli->real_escape_string($Team1_score);
		$Team2_score = $mysqli->real_escape_string($Team2_score);
		$game_time = $mysqli->real_escape_string($game_time);
		
		// may have problem. how to add id.
		$result = $mysqli->query("REPLACE into Game values(0,'$Team1','$Team2','$Team1_score','$Team2_score','$game_time')"); 
		if($result){
			$id = $mysqli->insert_id;
			return new game($id, $Team1, $Team2, $Team1_score, $Team2_score, $game_time);
		}else{
			printf("SQL error: %s\n", $mysqli->error);
		}
		return null;
	}
	
	public static function update($game_id, $Team1, $Team2, $Team1_score, $Team2_score){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		
		$result = $mysqli->query("UPDATE Game " . 
									"SET Team1 = '$Team1', Team2 = '$Team2', Team1_score = '$Team1_score', Team2_score = '$Team2_score' " .
									"WHERE game_id = '$game_id' "); 
		if($result){
			$id = $mysqli->insert_id;
			return $id;  // should be the same as $game_id.
		}else{
			printf("SQL Update error: %s\n", $mysqli->error);
		}
		return null;
	}
	
	public static function addEventToGame($game_id, $type, $first, $last, $score, $team){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		
		// First: add the score to specific team, if team is found
		$teamSelected = $mysqli->query("select * from Game where (Team1 = '$team' || Team2 = '$team') && game_id = '$game_id'");
		if($teamSelected->num_rows == 0){
			header("HTTP/1.0 400 Bad Request");
			printMessageJSON("Bad Request, Team Not Match");
			exit();
		}
		$teamSelected = $teamSelected->fetch_row();
		$allAcore = [$teamSelected[1] => $teamSelected[3], $teamSelected[2] => $teamSelected[4]];
		
		if($type == "fieldgoal"){
			$allAcore[$team] += 3;
		}else if($type == "passing"){
			$allAcore[$team] += 7;
		}else if($type == "rushing"){
			$allAcore[$team] += 7;
		}
		
		// if the score is not match, exit();
		if($allAcore[$team] != $score){
			header("HTTP/1.0 400 Bad Request");
			printMessageJSON("Bad Request, Score Not Match");
			exit();	
		}

		if($allAcore[$teamSelected[1]] >= $allAcore[$teamSelected[2]]){
			$winner = [$teamSelected[1] => $allAcore[$teamSelected[1]]]; $loser = [$teamSelected[2] => $allAcore[$teamSelected[2]]];
		}else{
			$winner = [$teamSelected[2] => $allAcore[$teamSelected[2]]]; $loser = [$teamSelected[1] => $allAcore[$teamSelected[1]]];
		}
		
		// Update the team score in Game Table.
		$gameUpdated = game::update($game_id, key($winner), key($loser), current($winner), current($loser));
		
		// Second: find the player in the team specified.
		// if it exists, return p_id, if not, insert it and return the p_id.
		$player_id = $mysqli->query("select player_id from Player where first_name = '$first' && last_name = '$last' && team_name = '$team'");
	
		if($player_id->num_rows == 0){
			$newPlayer = player::insert($first, $last, $team);
		    $newPlayer = (array)($newPlayer);
		    
		    $player_id = intval($newPlayer["player_id"]);
		}else{
			$player_id = $player_id->fetch_row()[0];
		}
		
		// Use the p_id and game_id to find the event of the type, 
		// if it exists, increase the number of the type
		// if not, insert the event.
		$eventSelected = $mysqli->query("select event_id from Event where p_id = '$player_id' && g_id = '$game_id'");
		$cnt = ["rushing" => 0, "passing" => 0, "fieldgoal" => 0, "passTo" => NULL];
		$cnt[$type] ++;
		
		if($eventSelected->num_rows == 0){
			$event_id = event::insert($player_id, $game_id, $cnt["rushing"], $cnt["passing"], $cnt["fieldgoal"], $cnt["passTo"]);
		}else{
			$event_id = event::update($player_id, $game_id, $cnt["rushing"], $cnt["passing"], $cnt["fieldgoal"], $cnt["passTo"]);
		}
		// var_dump($event_id);
		$ids = [$game_id, $player_id];
		return $ids;
	}
	
	public static function getAllIDs(){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		
		$result = $mysqli->query("select game_id from Game");
		$id_array = array();
		
		if ($result) {
	      	while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['game_id']);
	      	}
	    }else{
			printf("SQL error: %s\n", $mysqli->error);
		}
	    return $id_array;
	}
	
	public static function getAllIDsbyTeam($team_name){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		
		$result = $mysqli->query("select distinct game_id from Game where Team1 = '$team_name' or Team2 = '$team_name' ");
		$id_array = array();
		
		if ($result) {
	      	while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['game_id']);
	      	}
	    }else{
			printf("SQL error: %s\n", $mysqli->error);
		}
	    return $id_array;
	}
	
	public static function getAllIDsbyDate($date_start, $date_end){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		if($date_start == ""){
			$result = $mysqli->query("select distinct game_id from Game where time <= '$date_end' ");
		}else if($date_end == ""){
			$result = $mysqli->query("select distinct game_id from Game where time >= '$date_start' ");
		}else{
			$result = $mysqli->query("select distinct game_id from Game where time <= '$date_end' and time >= '$date_start' ");
		}
		$id_array = array();
		
		if ($result) {
	      	while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['game_id']);
	      	}
	    }else{
			printf("SQL error: %s\n", $mysqli->error);
		}
	    return $id_array;
	}
	
	public static function findByID($game_id){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		$game_id = intval($game_id);
		$sqlQuery = "select distinct game_id, time, Team1, Team1_score, Team2, Team2_score " . 
					"from Game " . 
					"where game_id = '$game_id' "; // The space of each line is necessary for SQL syntax.
		$result = $mysqli->query($sqlQuery);
		
		if ($result) {
	      	$next_row = $result->fetch_row();
	      	$data_id = array();
	      	if($next_row != NULL){
		      	$length = count($next_row);
		      	$data_id = ["id" => $next_row[0], "time" => $next_row[1], "team1" => $next_row[2], "team2" => $next_row[4], "team1_score" => $next_row[3], "team2_score" => $next_row[5]];
		      	
		      	$sqlQuery = "select team_name, first_name, last_name, rushing, passing, fieldgoal " . 
							"from Game, Event, Player " . 
							"where game_id = '$game_id' && g_id = '$game_id' &&  p_id = player_id "; // The space of each line is necessary for SQL syntax.
				$result = $mysqli->query($sqlQuery);
				$team1_scoring = array();
				$team2_scoring = array();
				
				if ($result) {
			      	while ($next_row = $result->fetch_array()) {
			      		
						$scoreType = ["rushing", "passing", "fieldgoal"];
						if($next_row[0] == $data_id["team1"]){
							for($i = 0; $i < 3; $i ++){
								for($j = 0; $j < $next_row[3 + $i]; $j ++){
									$team1_scoring[] = ["type" => $scoreType[$i], 
														"player" => ["first" => $next_row[1], "last" =>  $next_row[2]]];
								}
							}
						}else{
							// $next_row[0] == $data_id[$team2]
							for($i = 0; $i < 3; $i ++){
								for($j = 0; $j < $next_row[3 + $i]; $j ++){
									$team2_scoring[] = ["type" => $scoreType[$i], 
														"player" => ["first" => $next_row[1], "last" =>  $next_row[2]]];
								}
							}
						}
			      	}
				}
				$data_id["team1_scoring"] = $team1_scoring;
				$data_id["team2_scoring"] = $team2_scoring;
	      	}
	    }else{
			printf("SQL error: %s\n", $mysqli->error);
		}
	    return $data_id;
	}
	public static function delete($game_id){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		$mysqli->query("delete from Event where g_id = '$game_id'" );
		$mysqli->query("delete from Game where game_id = '$game_id'" );
		if($mysqli->error){
			printf("SQL error: %s\n", $mysqli->error);
			return;
		}
	}
}	
?>

