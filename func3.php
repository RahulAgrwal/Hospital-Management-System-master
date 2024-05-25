<?php
session_start();
//$con=mysqli_connect("localhost","root","","myhmsdb");
if (isset($_POST['adsub'])) {
	$username = $_POST['username1'];
	$password = $_POST['password2'];
	if($username=="admin" && $password=="admin123"){
	$_SESSION['username'] = "admin";
	header("Location:admin-panel1.php");
	}

}
