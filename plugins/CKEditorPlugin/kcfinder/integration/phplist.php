<?php

// KCfinder integration with phpList
// allow for custom config file
if (isset($_SERVER['ConfigFile']) && is_file($_SERVER['ConfigFile'])) {
    $configfile = $_SERVER['ConfigFile'];
}

if (is_file($configfile) && filesize($configfile) > 20) {
    include_once $configfile;
}
ini_set('session.name','phpListSession');
ini_set('session.cookie_samesite','Strict');
ini_set('session.use_only_cookies',1);
ini_set('session.cookie_httponly',1);

@session_start();

if (empty($_SESSION['adminloggedin'])) {
    die('Not logged in');
}
