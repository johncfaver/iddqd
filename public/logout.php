<?php
	require('config.php');
    try{
        $dbconn = new PDO("pgsql:host=$dbhost;dbname=$dbname;port=$dbport",$dbuser,$dbpass);
        $dbconn->query("DELETE FROM tokens WHERE userid=".$_SESSION['userid']);
    }catch(PDOException $e){
    }
	session_start();
    session_unset();
    session_destroy();
    returnhome(0);
?>
