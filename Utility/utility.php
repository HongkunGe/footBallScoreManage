<?php
	function getJSON($data){
		print(json_encode($data, JSON_PRETTY_PRINT));
		return;
	}
	
	function validateParamter(){
		$args = func_get_args();
		foreach ($args as $arg){
			if (!isset($_REQUEST[$arg])) {
				header("HTTP/1.0 400 Bad Request");
				print("Missing '$arg'");
				exit();
		    }
		}
	}
	
	function printMessageJSON($msg){
		header("Content-type: application/json");
		$msgArray = ["message" => $msg];
		print(json_encode($msgArray, JSON_PRETTY_PRINT));
	}
?>