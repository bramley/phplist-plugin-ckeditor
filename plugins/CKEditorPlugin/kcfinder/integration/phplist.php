<?php

// KCfinder integration with phpList
// allow for custom config file
if (isset($_SERVER['ConfigFile']) && is_file($_SERVER['ConfigFile'])) {
    $configfile = $_SERVER['ConfigFile'];
}

if (is_file($configfile) && filesize($configfile) > 20) {
    include_once $configfile;
}
@session_start();

if (empty($_SESSION['adminloggedin'])) {
    die('Not logged in');
}
