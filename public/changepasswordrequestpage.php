<?php
	require('../private/cred.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}
    
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>Change Password Request</title>
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
        <form method="post" action="cgi-bin/passwordrequest.py" id="register" >
            Username: <input type="text" id="username" name="username" size="8" /><br />
            Email: <input type="text" id="email" name="email" size="8" /><br /><br />
            <input type="submit" value="Request Password Change" />	
        </form>
	</span>
</div>
</body>
</html>
