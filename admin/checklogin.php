<?php
function check_login()
{
    if(!isset($_SESSION['alogin']) or $_SESSION['alogin'] == '' )  {
        $host = $_SERVER['HTTP_HOST'];
        $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = "login.php";        
        $_SESSION["login"] = "";
        header("Location: $extra");
        exit();
    } 
    
    if ($_SESSION['role'] != 'admin') {
		$host=$_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra="dashboard.php";	
        header("Location: $extra");
        exit();
    }
}
?>