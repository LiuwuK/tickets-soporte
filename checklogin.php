<?php
function check_login()
{
	if(!isset($_SESSION['login']) or $_SESSION['login'] == '' ) {	
		$host=$_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra="login.php";		
		$_SESSION["login"]="";
		header("Location: $extra");	
		exit();
	} else if ($_SESSION['role'] != 'user') {
		$host=$_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra="home.php";	
        header("Location: $extra");
        exit();
    }
}
?>