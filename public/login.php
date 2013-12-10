<?php
	require 'config.php';

	$inputusername=pg_escape_string($_POST['enteredusername']);
	$inputpassword=pg_escape_string($_POST['enteredpassword']);

	try{
		$dbconn = new PDO("pgsql:host=$dbhost;dbname=$dbname;port=$dbport",$dbuser,$dbpass);
	}catch(PDOException $e){
		//echo 'Connection to database failed: '. $e->getMessage();
	}
	$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$q = $dbconn->prepare("SELECT * from users where username=:name and password=crypt(:pass,password)");
	$q->bindParam(":name",$inputusername,PDO::PARAM_STR);
	$q->bindParam(":pass",$inputpassword,PDO::PARAM_STR);
	$q->execute();
	if($q->rowCount()==1){	
		session_start();
		$userarray=$q->fetch();
		$_SESSION['username']=$userarray['username'];	
		$_SESSION['userid']=$userarray['userid'];
		$_SESSION['notebook_molids']=Array();
        $_SESSION['notebook_bountyids']=Array();
        $token = bin2hex(openssl_random_pseudo_bytes(20));
        $_SESSION['token'] = $token;
        $q2 = $dbconn->query("DELETE FROM tokens WHERE userid=".$userarray['userid']);
        $q3 = $dbconn->query("INSERT INTO tokens (userid,token,startdate,enddate) VALUES 
                                  (".$userarray['userid'].",".$token.",localtimestamp, localtimestamp+interval '1 day') ");
        $dbconn->commit();
        $dbconn=null;
        if($q3 !== false){
		    $loggedin=True;	
            returnhome(0);
        }else{
            returnhome(1);
            exit;
        }
    }
    else{
        $dbconn=null;
        $loggedin=False;
        header('Location: index.php?status=badpw');
        exit;
    }
?>
