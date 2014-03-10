<?php
	require('config.php');
    session_start();
   
    try{
        $dbconn = new PDO("pgsql:host=$dbhost;dbname=$dbname;port=$dbport",$dbuser,$dbpass);
        $q = $dbconn->prepare("DELETE FROM tokens WHERE userid=:num");
        $q->bindParam(":num",$_SESSION['userid'],PDO::PARAM_INT);
        $q->execute();
    }catch(PDOException $e){
        returnhome(43);
    }
	
    session_unset();
    session_destroy();
    returnhome(0);
?>
