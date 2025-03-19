<?php
define('BASE_URL', '/tickets-soporte/');
define('BASE_PATH', __DIR__ . '/');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_login()
{
    $session_expiration_time = 6 * 60 * 60; 

    // Verificar si la sesión está activa y si el usuario no está logueado
    if (!isset($_SESSION['login']) || $_SESSION['login'] == '') {
        $current_page = basename($_SERVER['PHP_SELF']);
        if ($current_page != "login.php") {
            header("Location: /tickets-soporte/login.php");
            exit();
        }
    } 
    
    // Si está logueado y está en login.php, redirige a dashboard.php
    if (isset($_SESSION['login']) && $_SESSION['login'] != '' && basename($_SERVER['PHP_SELF']) == 'login.php') {
		if($_SESSION['role'] == 'admin'){
			header("Location: admin/home.php");
		}else{
			header("Location: dashboard.php");
		}
        exit();
    }
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_expiration_time) {
        session_unset();
        session_destroy();
        header("Location:".BASE_URL."login.php?session_expired=1");
        exit();
    }

    // Actualizar el tiempo de la última actividad
    $_SESSION['last_activity'] = time();
}
?>