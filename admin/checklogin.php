<?php
function check_login()
{
    if (strlen($_SESSION['id']) == 0) {
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