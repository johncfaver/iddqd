<?php
	require('../private/cred.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}
    $key = (isset($_GET['key']))?pg_escape_string($_GET['key']):0;
   
    $q = $dbconn->prepare("SELECT p.userid,p.daterequested,p.datechanged,u.username from passwordchanges p left join users u on p.userid=u.userid where p.changekey=:str");
    $q->bindParam(":str",$key,PDO::PARAM_STR);
    $q->execute();
    $r = $q->fetch(PDO::FETCH_ASSOC);  
    if(sizeof($r) != 4){
        $key=0; 
    }
    
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>Change Password</title>
	<link rel="stylesheet" href="reset.css" type="text/css" />
	<link rel="stylesheet" href="iddqd.css" type="text/css" />
</head>
<body>

<div id="div_left">
</div>	
<div id="div_top">
	<div id="div_login">
	</div>	
</div>
<div id="div_main">
    <span id="regspan" style="text-align:right;float:right;margin-right:450px;margin-top:50px">

<?php
    if(!$key){
        echo 'No key provided!';
    }else{
        if(isset($_GET['passwordisbad'])){
            echo 'That password was invalid.<br /><br/>';
        }
        
	    echo '<form method="post" action="changepw.php" id="register" >';
		echo 'Username: '.$r['username'].'<br /><br/>';
        echo 'New Password: <input type="password" id="desiredpassword" name="desiredpassword" size="8" /><br /><br />';
		echo '<input type="submit" value="Change Password" />	';
        echo '<input type="hidden" name="key" value="'.$key.'" />';
        echo '<input type="hidden" name="username" value="'.$r['username'].'" />';
	    echo '</form>';
    }
?>
	</span>
</div>
</body>
</html>
