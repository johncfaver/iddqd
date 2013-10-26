<?php
	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}

	$desiredusername=pg_escape_string($_POST['desiredusername']);
	$desiredpassword=pg_escape_string($_POST['desiredpassword']);
	$desiredemail=pg_escape_string($_POST['desiredemail']);

    $desiredusernamelength = strlen($desiredusername); 
    $desiredpasswordlength = strlen($desiredpassword); 
   
    //Check desiredusername 
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
    if($desiredpasswordlength < 5 or $desiredpasswordlength > 20){
        header('Location: registerpage.php?passwordisbad=1');
        exit;
    }
       
	$q = $dbconn->prepare("SELECT username,email from users where username=:str1 or email=:str2");
	$q->bindParam(":str1",$desiredusername,PDO::PARAM_STR);
    $q->bindParam(":str2",$desiredemail,PDO::PARAM_STR);
	$q->execute();	
	if($q->rowCount()==0){
		$nameexists=False;	
	}else{
		$nameexists=True;
	}
	if(!$nameexists){
		$q = $dbconn->prepare("INSERT INTO users(username,password,email) values(:str1,crypt(:str2,gen_salt('bf')),:str3)");
		$q->bindparam(":str1",$desiredusername,PDO::PARAM_STR);
		$q->bindparam(":str2",$desiredpassword,PDO::PARAM_STR);
        $q->bindparam(":str3",$desiredemail,PDO::PARAM_STR);
		$q->execute();
		$q = $dbconn->prepare("SELECT * from users where username=:name");
		$q->bindparam(":name",$desiredusername,PDO::PARAM_STR);
		$q->execute();
		$u=$q->fetch();
		session_start();
		$_SESSION['username']=$u['username'];
		$_SESSION['userid']=$u['userid'];
		$loggedin=True;
		$dbconn=null;	
		header("Location: index.php");
        exit;
	}else{
		$dbconn=null;
		header("Location: registerpage.php?nameexists=1");		
        exit;
	}

?>
