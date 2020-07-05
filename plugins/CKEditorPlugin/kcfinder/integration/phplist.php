<?php

function phplistSession()
{
    // Use the same session initialisation as phplist in file init.php
    ini_set('session.name', 'phpListSession');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);

    @session_start();

    if (empty($_SESSION['adminloggedin'])) {
        die('Not logged in');
    }
}
phplistSession();
