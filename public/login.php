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
        $_SESSION['isadmin']=$userarray['isadmin'];
		$_SESSION['notebook_molids']=Array();
        $_SESSION['notebook_bountyids']=Array();
        $token = bin2hex(openssl_random_pseudo_bytes(20));
        $_SESSION['token'] = $token;
        $q2 = $dbconn->prepare("DELETE FROM tokens WHERE userid=:num");
        $q2->bindParam(":num",$userarray['userid'],PDO::PARAM_INT);
        $q2->execute();
        $q3 = $dbconn->prepare("INSERT INTO tokens (userid,token,startdate,enddate) VALUES 
                                  (:num,:str,localtimestamp, localtimestamp+interval '1 day') ");
        $q3->bindParam(":num",$userarray['userid'],PDO::PARAM_INT);
        $q3->bindParam(":str",$token,PDO::PARAM_STR);
        $q3->execute();
        
        $dbconn=null;
        if($q3 !== false){
            returnhome(0);
            exit;
        }else{
            session_unset();
            session_destroy();
            returnhome(1);
            exit;
        }
    }
    else{
        $dbconn=null;
        header('Location: index.php?status=badpw');
        exit;
    }
?>
