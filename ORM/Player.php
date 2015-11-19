<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

	
class player{
	private $player_id;
	private $first_name;
	private $last_name;
	private $team_name;
	
	private function __construct($player_id, $first_name, $last_name, $team_name){
		$this->player_id = $player_id;
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->team_name = $team_name;
	}
	
	public static function insert($first_name, $last_name, $team_name){
		$mysqli = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
		
		/*check the connection*/
		if(mysqli_connect_errno()){
			printf("Connection failed: %s\n", mysqli_connect_errno);
			exit();
		}
		
		// $player_id = $mysqli->real_escape_string($player_id);
		$first_name = $mysqli->real_escape_string($first_name);
		$last_name = $mysqli->real_escape_string($last_name);
		$team_name = $mysqli->real_escape_string($team_name);
		
		// may have problem. how to add id.
		$result = $mysqli->query("REPLACE into Player values(0, '$first_name', '$last_name', '$team_name')"); 
		if($result){
			$id = $mysqli->insert_id;
			return new player($id, $first_name, $last_name, $team_name);
		}else{
			printf("SQL error: %s\n", $mysqli->error);
		}
		return null;
	}
	
}	
?>

