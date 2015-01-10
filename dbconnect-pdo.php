<?php

/*** mysql hostname ***/
$hostname = 'host';

/*** mysql username ***/
$username = 'user';

/*** mysql password ***/
$password = 'password';

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=database", $username, $password);
    }
catch(PDOException $e)
    {
    echo $e->getMessage();
    }
    
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>