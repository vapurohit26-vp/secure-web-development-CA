<?php

ini_set('session.use_strict_mode', 1);         
ini_set('session.cookie_httponly', 1);         
ini_set('session.cookie_samesite', 'Strict');  

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

session_start();


$max_inactivity        = 15 * 60;   
$max_session_lifetime  = 2 * 60 * 60; 

if (!empty($_SESSION['user_id'])) {

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $max_inactivity) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit;
    }

    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $max_session_lifetime) {
        session_unset();
        session_destroy();
        header("Location: login.php?session_expired=1");
        exit;
    }


    $_SESSION['last_activity'] = time();
}
