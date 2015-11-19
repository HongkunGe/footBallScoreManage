<?php
	
	
class event{
	private $event_id;
	private $player_id;
	private $game_id;
	private $rushing;
	private $passing;
	private $fieldgoal;
	private $passTo;
	
	private function __construct($event_id, $player_id, $game_id, $rushing, $passing, $fieldgoal, $passTo){
		$this->event_id = $event_id;
		$this->player_id = $player_id;
		$this->game_id = $game_id;
		$this->rushing = $rushing;
		$this->passing = $passing;
		$this->fieldgoal = $fieldgoal;
		$this->passTo = $passTo;
	}
	
	public static function insert($player_id, $game_id, $rushing, $passing, $fieldgoal, $passTo){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		
		// $player_id, $game_id are foreign keys of event table which are correlated with player and game.
		$result = $mysqli->query("REPLACE into Event values(0,'$player_id','$game_id','$rushing','$passing','$fieldgoal', '$passTo')"); 
		if($result){
			$id = $mysqli->insert_id;
			return new event($id, $player_id, $game_id, $rushing, $passing, $fieldgoal, $passTo);
		}else{
			printf("SQL error: %s\n", $mysqli->error);
		}
		return null;
	}
	
	public static function update($player_id, $game_id, $rushing, $passing, $fieldgoal, $passTo){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		
		$result = $mysqli->query("UPDATE Event " . 
									"SET rushing = rushing + '$rushing', passing = passing + '$passing', fieldgoal = fieldgoal + '$fieldgoal', passTo = '$passTo' " .
									"WHERE p_id = '$player_id' AND g_id = '$game_id' "); 
		if($result){
			$id = $mysqli->insert_id;
			return new event($id, $player_id, $game_id, $rushing, $passing, $fieldgoal, $passTo);
		}else{
			printf("SQL Update error: %s\n", $mysqli->error);
		}
		return null;
	}
	
}	
?>

