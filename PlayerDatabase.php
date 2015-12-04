<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/New_York');

require_once('ORM/Player.php');

$conn = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");

if(mysqli_connect_errno()){
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

$conn->query("drop table if exists Player");
$conn->query("create table Player (" .
			 	"player_id int primary key not null auto_increment, " .
			 	"first_name char(100), " .
			 	"last_name char(100), " .
			 	"team_name char(100))");

$all_game_data = file_get_contents('http://www.cs.unc.edu/Courses/comp426-f15/kmp/a3/a3-data.txt');
$all_game_data = explode("\n", $all_game_data);


// insert to Player table.
$length = count($all_game_data);

for($i = 0; $i < $length - 1; $i++){
	$player_i =  explode(" ", $all_game_data[$i]);

	//printf($player_i[0] . $player_i[1] . $player_i[2]);
	$fn = $player_i[0];
	$ln = $player_i[1];
	$tn = $player_i[2];
	$exist = $conn->query("select * from Player where first_name = '$fn' && last_name = '$ln' && team_name = '$tn'");
	if($exist->num_rows != 0) continue;
	
	?>
	<br>
	<?php
	
	//printf("%d insert player %s %s  ", $i, $player_i[0], $player_i[1]);
	$result = player::insert($player_i[0], $player_i[1], $player_i[2]);
	
	$player_id = $result->player_id;
	$first_name = $result->first_name;
	$last_name = $result->last_name;
	printf("insert Player " . "$player_id: " . "$first_name". " and ". "$last_name". " succeeded!");
	
	if(count($player_i) == 8){
		
	?>
	<br>
	<?php
		//printf("%d insert Passing player %s %s ", $i, $player_i[6], $player_i[7]);
		$result = player::insert($player_i[6], $player_i[7], $player_i[2]);
		
		$player_id = $result->player_id;
		$first_name = $result->first_name;
		$last_name = $result->last_name;
		printf("insert Player " . "$player_id: " . "$first_name". " and ". "$last_name". " succeeded!");
	}
}
?>
<br>
<?php
	// $exist = $conn->query("select * from Player p where ");
	// if($exist){
	// 	$next_row = $exist->fetch_assoc();
	// 	printf($next_row["first_name"]);
	// 	printf(count($next_row));
	// }
	// if($exist->num_rows != 0) continue;
		
	// 	continue;
	// }else{
	// 	printf($player_i[0] . $player_i[1] . $player_i[2]);
	// 	printf("YES");
	// }
?>

