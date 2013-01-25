<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Register New Account</title>
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
<?php
	if(isset($_GET['nameexists'])){
		echo 'That username doesn\'t exist!';
	}
?>
	<span id="regspan" style="text-align:right;float:right;margin-right:450px;margin-top:50px">
	<form method="post" action="changepw.php" id="register" >
		Username: <input type="text" id="desiredusername" name="desiredusername" size="8" /><br />
		New Password: <input type="password" id="desiredpassword" name="desiredpassword" size="8" /><br /><br />
		<input type="submit" value="Change Password" />	
	</form>
	</span>
</div>
</body>
</html>
