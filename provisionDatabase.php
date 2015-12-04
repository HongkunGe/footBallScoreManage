<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/New_York');


$conn = new mysqli("classroom.cs.unc.edu", "hongkun", "CH@ngemenow99Please!hongkun", "hongkundb");

if(mysqli_connect_errno()){
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

$conn->query("drop table if exists Event");
$conn->query("drop table if exists Game");
$conn->query("drop table if exists Player");

?>