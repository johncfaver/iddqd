<?php
	require('/home/faver/bin/cred.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}

	$desiredusername=pg_escape_string($_POST['desiredusername']);
	$desiredpassword=pg_escape_string($_POST['desiredpassword']);
 
 
    $forbiddenchars=Array("'",">","<","/","\\","\"",'&','*','$','#',';');
    foreach($forbiddenchars as $c){
        if(strpos($desiredusername,$c)!==false){
            header('Location: registerpage.php?nameisbad=1');
            exit;
        }
    }
	$q = $dbconn->prepare("SELECT username from users where username=:name");
	$q->bindParam(":name",$desiredusername,PDO::PARAM_STR);
	$q->execute();	
	if($q->rowCount()==0){
		$nameexists=False;	
	}else{
		$nameexists=True;
	}
	if(!$nameexists){
		$q = $dbconn->prepare("INSERT INTO users(username,password) values(:name,crypt(:pass,gen_salt('md5')))");
		$q->bindparam(":name",$desiredusername,PDO::PARAM_STR);
		$q->bindparam(":pass",$desiredpassword,PDO::PARAM_STR);
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
