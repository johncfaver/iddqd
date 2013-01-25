<?php
	require '/home/faver/bin/cred.php';

	$inputusername=pg_escape_string($_POST['enteredusername']);
	$inputpassword=pg_escape_string($_POST['enteredpassword']);

	try{
		$dbconn = new PDO("pgsql:host=$dbhost;dbname=$dbname;port=$dbport",$dbuser,$dbpass);
	}catch(PDOException $e){
		echo 'Connection to database failed: '. $e->getMessage();
	}
	$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$regq = $dbconn->prepare("SELECT * from users where username=:name and password=crypt(:pass,password)");
	$regq->bindParam(":name",$inputusername,PDO::PARAM_STR);
	$regq->bindParam(":pass",$inputpassword,PDO::PARAM_STR);
	$regq->execute();
	if($regq->rowCount()==1){	
		session_start();
		$userarray=$regq->fetch();
		$_SESSION['username']=$userarray['username'];	
		$_SESSION['userid']=$userarray['userid'];
		$loggedin=True;	
             	$dbconn=null;
                returnhome();
        }
        else{
		$dbconn=null;
                $loggedin=False;
                header('Location: index.php?s=badpw');
        }
?>
