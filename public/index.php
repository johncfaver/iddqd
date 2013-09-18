<?php
	require('../private/cred.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}
	session_start();
	$loggedin = (isset($_SESSION['username']));
?>
<!DOCTYPE html>
<html>
<head>
	<title>IDDQD - Main</title>
	<link rel="stylesheet" href="reset.css" type="text/css" />
	<link rel="stylesheet" href="iddqd.css" type="text/css" />
	<script type="text/javascript" src="iddqd.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>
<div id="div_left">
	<div id="left_links">
<?php
	if($loggedin){
		echo '<span class="nonlinks">';
		echo '<a href="index.php" style="color:#bbbbff">Home</a><br /><br />';
		echo '<a href="search.php" style="color:white">Search</a> <br /><br />';
		echo '<a href="molecules.php" style="color:white">View Library</a><br /><br />';
		echo '<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />';
        echo '<a href="bounties.php" style="color:white">Bounties</a><br /><br />';
		echo '<a href="targets.php" style="color:white">Targets</a><br /><br />';
		echo '<a href="help.php" style="color:white">Help</a><br /><br />';
		echo '</span>';
	}
?>
	</div>
</div>	
<div id="div_top">
<?php
		if($loggedin){
			echo '<div id="div_notebook">';
			echo '<a href="notebook.php">My Notebook: '.count($_SESSION['notebook_molids']).'</a>';
			echo '</div>';
			echo '<div id="div_login">';
			echo '<span id="span_loggedin">Logged in as '.$_SESSION['username'].' <a href="logout.php">(logout)</a></span>';
		}else{		
			echo '<div id="div_login">';
			echo '<a href="registerpage.php" style="float:left;font-size:0.8em;">Create Account</a><br />';
			echo '<form id="login" method="post" action="login.php"	>';
			echo '<input type="text" value="username" name="enteredusername" id="enteredusername" onclick="clearuserbox();" size="7" />';
			echo '<input type="password" name="enteredpassword" value="password" id="enteredpassword" onclick="clearpasswordbox();" size="7" />';
			echo '<input type="submit" value="Log in"/>';
			echo '</form>';
		}
 		if(isset($_GET['status'])){
            echo '<span style="font-size:0.8em;position:fixed;top:60px;left:960px;margin:0px;">';
	        if($_GET['status']=='badpw'){
           	    echo 'Invalid username/password.';
            }elseif($_GET['status']=='error'){
				echo 'An error occurred.';	
			}
			echo '</span>';
        }
		echo '</div>';
?>	
</div>
<div id="div_main">
	<br /><br />
	Inhibitor Discovery, Design, and Quantification Database
</div>
</body>
</html>