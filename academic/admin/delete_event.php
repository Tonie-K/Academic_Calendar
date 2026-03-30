<?php
include "../config.php";

$id=$_GET['id'];

$stmt=$conn->prepare("DELETE FROM events WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

header("Location: dashboard.php");
?>