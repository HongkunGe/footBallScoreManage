<?php
	
	
class game{
	private $game_id;
	private $Team1;
	private $Team2;
	private $Team1_score;
	private $Team2_score;
	private $game_time;
	
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
	
}	
?>

