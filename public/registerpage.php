<!DOCTYPE html>
<html>
<head>
	<title>Register New Account</title>
	<link rel="stylesheet" href="reset.css" type="text/css" />
	<link rel="stylesheet" href="iddqd.css" type="text/css" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>

<div id="div_left"></div>	
<div id="div_top">
	<div id="div_login"></div>	
</div>
<div id="div_main">
<?php
	if(isset($_GET['nameexists'])){
		if((int)$_GET['nameexists']==1){
			echo 'That username exists already.<br />';
		}
	}
    if(isset($_GET['usernameisbad'])){
        if((int)$_GET['usernameisbad']==1){
            echo 'That username was awful. Try again.<br />';
        }
    }
    if(isset($_GET['passwordisbad'])){
        if((int)$_GET['passwordisbad']==1){
            echo 'That password was awful. Try again.<br />';
        }
    }
?>
	<span id="regspan" style="text-align:right;float:right;margin-right:450px;margin-top:50px">
	<form method="post" action="register.php" id="register" >
		Username  <input type="text" id="desiredusername" name="desiredusername" size="8" />  <br /><span style="font-size:0.7em;">(3-20 valid characters)</span><br /><br />
		Password: <input type="password" id="desiredpassword" name="desiredpassword" size="8" /> <br /><span style="font-size:0.7em;">(5-20 characters)</span><br /><br />
		<input type="submit" value="Register" />	
	</form>
	</span>
</div>
</body>
</html>
