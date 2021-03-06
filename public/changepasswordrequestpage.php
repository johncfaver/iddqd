<?php

// changepasswordrequestpage.php
// Request a password change email be sent to an email address

	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		//echo 'Database connection failed: '. $e->getMessage();
	}
    $status = isset($_GET['status'])?pg_escape_string($_GET['status']):0;
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
<div id="div_holder">
<div id="div_left">
</div>	
<div id="div_top">
	<div id="div_login">
	</div>	
</div>
<div id="div_main">
    <span id="span_regspan">
<?php
        if($status==='bademail'){
            echo 'That email wasn\'t found.<br /><br/>';
        }
        if($status==='openrequest'){
            echo 'An email was sent to that address earlier today. Try again tomorrow. <br /><br />';
        }
?>
        <form method="post" action="../cgi-bin/passwordrequest.py" id="register" >
            Email: <input type="text" id="email" name="email" size="15" required maxlength="40" />
            <br /><br /><br/><br/>
            <input type="submit" value="Request Password Change" />	
        </form>
	</span>
</div>
</div>
</body>
</html>
