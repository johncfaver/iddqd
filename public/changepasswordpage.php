<?php
//  changepasswordpage.php
//  Form to change password, link is sent in email to user.
//  Must have valid password change key to change password.

	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
	//	echo 'Database connection failed: '. $e->getMessage();
	}
    $key = (isset($_GET['key']))?pg_escape_string($_GET['key']):0;
   
    $q = $dbconn->prepare("SELECT 
                            p.userid,p.daterequested,u.username 
                           FROM passwordchanges p LEFT JOIN users u ON p.userid=u.userid 
                           WHERE p.changekey=:str AND p.datechanged IS NULL");
    $q->bindParam(":str",$key,PDO::PARAM_STR);
    $q->execute();
    $r = $q->fetch(PDO::FETCH_ASSOC);  
    if(sizeof($r) != 3){
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
    <span id="span_regspan">

<?php
    if(!$key){
        echo 'No valid key provided!';
    }else{
        if(isset($_GET['passwordisbad'])){
            echo 'That password was invalid.<br /><br/>';
        }
	    echo '<form method="post" action="changepw.php" id="register" >
		        Username: '.$r['username'].'<br /><br/>
                New Password: <input type="password" id="desiredpassword" name="desiredpassword" size="8" />
                <br /><br />
		        <input type="submit" value="Change Password" />	
                <input type="hidden" name="key" value="'.$key.'" />
                <input type="hidden" name="username" value="'.$r['username'].'" />
	          </form>';
    }
?>
	</span>
</div>
</body>
</html>
