<?php
	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
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

<div id="div_left">
</div>	
<div id="div_top">
	<div id="div_login">
	</div>	
</div>
<div id="div_main">
    <span id="regspan" style="text-align:right;float:right;margin-right:450px;margin-top:50px">
<?php
        if($status==='bademail'){
            echo 'That email wasn\'t found.<br /><br/>';
        }
        if($status==='openrequest'){
            echo 'An email was sent to that address earlier today. Try again tomorrow. <br /><br />';
        }
?>
        <form method="post" action="cgi-bin/passwordrequest.py" id="register" >
            Email: <input type="text" id="email" name="email" size="8" /><br /><br />
            <input type="submit" value="Request Password Change" />	
        </form>
	</span>
</div>
</body>
</html>
