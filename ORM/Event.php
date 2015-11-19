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
		$Team1 = $mysqli->real_escape_string($Team1);
		$Team2 = $mysqli->real_escape_string($Team2);
		
		$rushing = $mysqli->real_escape_string($rushing);
		$passing = $mysqli->real_escape_string($passing);
		$fieldgoal = $mysqli->real_escape_string($fieldgoal);
		
		// may have problem. how to add id.
		$result = $mysqli->query("REPLACE into Game values(0,'$Team1','$Team2','$Team1_score','$Team2_score','$game_time')"); 
		if($result){
			$id = $mysqli->insert_id;
			return new Game($id, $Team1, $Team2, $Team1_score, $Team2_score, $game_time);
		}else{
			printf("SQL error: %s\n", $mysqli->error);
		}
		return null;
	}
	
}	
?>

