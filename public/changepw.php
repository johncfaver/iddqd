<?php

//  changepw.php
//  Perform a password update in the database.

	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		//echo 'Database connection failed: '. $e->getMessage();
	}
    
    if(!isset($_POST['key'])) returnhome(45);
    if(!isset($_POST['username'])) returnhome(46);
    if(!isset($_POST['desiredpassword'])) returnhome(47);

	$username=pg_escape_string($_POST['username']);
	$desiredpassword=pg_escape_string($_POST['desiredpassword']);
    
    $desiredpasswordlength = strlen($desiredpassword); 
    if($desiredpasswordlength < 5 or $desiredpasswordlength > 20){
        header('Location: changepasswordpage.php?passwordisbad=1&key='.$_POST['key']);
        exit;
    }

	$q = $dbconn->prepare("SELECT username from users where username=:name");
	$q->bindParam(":name",$username,PDO::PARAM_STR);
	$q->execute();	
	if($q->rowCount()==0){
		$nameexists=False;	
	}else{
		$nameexists=True;
	}
	if($nameexists){
		try{
            $dbconn->beginTransaction();
            $q = $dbconn->prepare("UPDATE users SET password = crypt(:pass,gen_salt('bf')) WHERE username=:name ");
		    $q->bindparam(":name",$username,PDO::PARAM_STR);
		    $q->bindparam(":pass",$desiredpassword,PDO::PARAM_STR);
			$q->execute();
            $q = $dbconn ->prepare("UPDATE passwordchanges set datechanged = localtimestamp, changed=true where changekey=:str ");
            $q->bindparam(":str",$_POST['key'],PDO::PARAM_STR);
            $q->execute();
            $dbconn->commit();
			$dbconn=null;	
            returnhome(0);
        }catch(Exception $e){
            returnhome(41);
        }
	}else{
		$dbconn=null;
        returnhome(48);
	}
?>
