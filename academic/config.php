<?php

$host="localhost";
$user="root";
$pass="";
$db="academic_calendar";

$conn=new mysqli($host,$user,$pass,$db);

if($conn->connect_error){
die("Database connection failed");
}

session_start();

$timeout=900;

if(isset($_SESSION['last_activity'])){
if(time()-$_SESSION['last_activity']>$timeout){
session_unset();
session_destroy();
header("Location: login.php");
exit();
}
}

$_SESSION['last_activity']=time();

?>