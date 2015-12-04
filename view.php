<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/New_York');

include 'provisionDatabase.php';
include 'PlayerDatabase.php';
include 'GameDatabase.php';
include 'EventDatabase.php';


$conn = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");
if(mysqli_connect_errno()){
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

//How many touchdowns did Peyton Manning score or pass when playing against Miami


$sql = 'SELECT sum(passing)
		FROM Player, Event, Game
		WHERE Player.first_name = "Peyton" and Player.last_name = "Manning" and Player.player_id = Event.passTo and
			  Event.g_id = Game.game_id and (Game.Team1 = "Miami" or Game.Team2 = "Miami")';
$result1 = $conn->query($sql);

printf("How many touchdowns did Peyton Manning score or pass when playing against Miami?");

?>
<br>
<?php
printf($result1->fetch_row()[0]);
?>
<br>
<?php


// List all the games (date and opposing team) that Tennessee won.


$sql = 'SELECT time, Team2
		FROM Game
		WHERE Team1 = "Tennessee"';
$result1 = $conn->query($sql);
// var_dump($result1);
printf("List all the games (date and opposing team) that Tennessee won.");
?>
<br>
<?php
for($i = 0; $i < $result1->num_rows; $i ++){
	$team1 = $result1->fetch_row();
	printf("%s %s", $team1[0], $team1[1]);
	?>
	<br>
	<?php
}


// How rushing touchdowns did Reggie Bush score in October?


$sql = 'SELECT passing + rushing
		FROM Player, Event, Game
		WHERE Player.first_name = "Reggie" and Player.last_name = "Bush" and Player.player_id = Event.p_id and 
			  Game.time >= "2015-10-01" and Game.time <= "2015-10-31" and Game.game_id = Event.g_id';
$result1 = $conn->query($sql);
// var_dump($result1);
printf("How rushing touchdowns did Reggie Bush score in October?");
?>
<br>
<?php
printf($result1->fetch_row()[0]);
?>