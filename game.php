<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('../ORM/Game.php');
require_once('../ORM/Event.php');
require_once('../ORM/Player.php');

$path_components = explode('/', $_SERVER['PATH_INFO']);

if($_SERVER['REQUEST_METHOD'] == "GET") {
	
	// var_dump($path_components);
	// printf(($path_components[1]));
	
	if((count($path_components) >= 2) && ($path_components[1] != "")){
		
		// get game IDs by date_start or date_end.
		if(isset($_REQUEST['date_end']) || isset($_REQUEST['date_start'])){
			
			$date_end = "";
			$date_start = "";
			
			if(isset($_REQUEST['date_end']) && trim($_REQUEST['date_end'])){
				$date_end = trim($_REQUEST['date_end']);
			}
			if(isset($_REQUEST['date_start']) && trim($_REQUEST['date_start'])){
				$date_start = trim($_REQUEST['date_start']);
			}
			
			if($date_end == "" && $date_start == ""){
				header("HTTP/1.0 400 Bad Request");
				print("Bad Date");
				exit();
		   	}
		   	// $date_end = new DateTime($date_str);
		   	var_dump($date_end);
		   	var_dump($date_start);
		   	$id_array = game::getAllIDsbyDate($date_start, $date_end);
			if($id_array == null){
				// no id_array found.
				header("HTTP/1.0 404 Not Found");
				print("Game before date " . $date_end . " not found.");
				exit();
			}
			

			header("Content-type: application/json");
			print(json_encode($id_array));
			exit();
		}
		
		// get game IDs by team.
		if(isset($_REQUEST['team'])){
			
		   	$team_name = trim($_REQUEST['team']);
		   	
		   	if($team_name == ""){
				header("HTTP/1.0 400 Bad Request");
				print("Bad Team Name");
				exit();
		   	}
		   	
			$id_array = game::getAllIDsbyTeam($team_name);
			
			if($id_array == null){
				// no id_array found.
				header("HTTP/1.0 404 Not Found");
				print("Game IDs with Team " . $team_name . " not found.");
				exit();
			}
			

			header("Content-type: application/json");
			print(json_encode($id_array));
			exit();
		}
		
		// return all game IDs as a  JSON array of integers.
		$id_array = game::getAllIDs();	
		
		if($id_array == null){
			// no id_array found.
			header("HTTP/1.0 404 Not Found");
			print("Index of Game IDs not found.");
			exit();
		}
		
		header("Content-type: application/json");
		print(json_encode($id_array));
		exit();
	}
}
?>