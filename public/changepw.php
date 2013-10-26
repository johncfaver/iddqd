<?php

//  Perform a password update.

	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}

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
            $q = $dbconn->prepare("UPDATE users set password = crypt(:pass,gen_salt('bf')) where username=:name returning username,userid");
		    $q->bindparam(":name",$username,PDO::PARAM_STR);
		    $q->bindparam(":pass",$desiredpassword,PDO::PARAM_STR);
			$q->execute();
            $q = $dbconn ->prepare("UPDATE passwordchanges set datechanged = localtimestamp, changed=true where changekey=:str ");
            $q->bindparam(":str",$_POST['key'],PDO::PARAM_STR);
            $q->execute();
            $dbconn->commit();
			$u=$q->fetch();
			session_start();
			$_SESSION['username']=$u['username'];
			$_SESSION['userid']=$u['userid'];
			$loggedin=True;
			$dbconn=null;	
			header("Location: index.php");
        }catch(Exception $e){
            header("Location: index.php?status=error ");
        }
	    exit;
	}else{
		$dbconn=null;
		header("Location: changepassword.php?nameexists=0");		
        exit;
	}

?>
