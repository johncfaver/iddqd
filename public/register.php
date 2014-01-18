<?php

//Handles user registration.

	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		//echo 'Database connection failed: '. $e->getMessage();
	}

    if(!isset($_POST['desiredusername'])){
        header('Location: registerpage.php?usernameisbad=1');
        exit;
    }
    if(!isset($_POST['desiredpassword'])){
        header('Location: registerpage.php?passwordisbad=1');
        exit;
    }
    if(!isset($_POST['desiredemail'])){
        header('Location: registerpage.php?emailisbad=1');
        exit;
    }
    if(!isset($_POST['invitekey'])){
        header('Location registerpage.php');
        exit;
    }

	$desiredusername = pg_escape_string($_POST['desiredusername']);
	$desiredpassword = pg_escape_string($_POST['desiredpassword']);
	$desiredemail = pg_escape_string($_POST['desiredemail']);
    $invitekey = pg_escape_string($_POST['invitekey']); 

//Check desiredusername 
    $desiredusernamelength = strlen($desiredusername); 
    if($desiredusernamelength < 3 or $desiredusernamelength > 20){
        header('Location: registerpage.php?usernameisbad=1');
        exit;
    }
    $forbiddenchars=Array("'",">","<","/","\\","\"",'&','*','$','#',';');
    foreach($forbiddenchars as $c){
        if(strpos($desiredusername,$c)!==false){
            header('Location: registerpage.php?usernameisbad=1');
            exit;
        }
    }
//Check desiredemail
    if(!filter_var($desiredemail, FILTER_VALIDATE_EMAIL)){
        header('Location: registerpage.php?emailisbad=1');
        exit;
    }
//Check desiredpassword 
    $desiredpasswordlength = strlen($desiredpassword); 
    if($desiredpasswordlength < 5 or $desiredpasswordlength > 20){
        header('Location: registerpage.php?passwordisbad=1');
        exit;
    }
//Check for valid inviation
	$q = $dbconn->prepare("SELECT email FROM invites WHERE invitekey=:str1 AND datejoined IS NULL");
	$q->bindParam(":str1",$invitekey,PDO::PARAM_STR);
	$q->execute();	
	if($q->rowCount()!=1){
        $dbconn=null;
		header("Location: registerpage.php");		
        exit;
	}
    $r = $q->fetch(PDO::FETCH_ASSOC);
    $invite_email = $r['email'];
    if($invite_email != $desiredemail){
        $dbconn=null;
		header("Location: registerpage.php");		
        exit;
    }

   
//Check for previous registration       
	$q = $dbconn->prepare("SELECT username,email from users where username=:str1 or email=:str2");
	$q->bindParam(":str1",$desiredusername,PDO::PARAM_STR);
    $q->bindParam(":str2",$desiredemail,PDO::PARAM_STR);
	$q->execute();	
	if($q->rowCount()!=0){
        $dbconn=null;
		header("Location: registerpage.php?nameexists=1");		
        exit;
	}


//Everything seems legit. Insert into user table, update invite table.
	$q = $dbconn->prepare("INSERT INTO users(username,password,email) values(:str1,crypt(:str2,gen_salt('bf')),:str3) RETURNING userid");
	$q->bindparam(":str1",$desiredusername,PDO::PARAM_STR);
	$q->bindparam(":str2",$desiredpassword,PDO::PARAM_STR);
    $q->bindparam(":str3",$desiredemail,PDO::PARAM_STR);
	$q->execute();
    $r = $q->fetch(PDO::FETCH_ASSOC);
    $newuserid=$r['userid'];

    $q = $dbconn->prepare("UPDATE invites SET datejoined=localtimestamp");
    $q->execute();

	session_start();
	$_SESSION['username']=$desiredusername;
	$_SESSION['userid']=$newuserid;
    $_SESSION['isadmin']=false;
    $_SESSION['notebook_molids']=Array();
    $_SESSION['notebook_bountyids']=Array();
    $token = bin2hex(openssl_random_pseudo_bytes(20));
    $_SESSION['token'] = $token;

    $q = $dbconn->prepare("INSERT INTO tokens (userid,token,startdate,enddate) VALUES 
                                  (:num,:str,localtimestamp, localtimestamp+interval '1 day') ");
    $q->bindParam(":num",$newuserid,PDO::PARAM_INT);
    $q->bindParam(":str",$token,PDO::PARAM_STR);
    $q->execute();

	$dbconn=null;	
    returnhome(0);

    exit;

?>
