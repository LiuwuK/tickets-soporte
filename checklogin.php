<?php
function check_login()
{
if(strlen($_SESSION['login'])==0)
	{	
		$host=$_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra="login.php";		
		$_SESSION["login"]="";
		header("Location: $extra");	
	}

    if ($_SESSION['role'] != 'user') {
		$host=$_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra="home.php";	
        header("Location: $extra");
        exit();
    }
}
?>