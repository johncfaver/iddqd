<?php
	require('/home/faver/bin/cred.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Connection failed: '. $e->getMessage();
	}
	session_start();
	$loggedin = (isset($_SESSION['username']))? True:False;

?>
<!DOCTYPE html>
<html>
<head>
<title>Main</title>
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
		echo '<a href="molecules.php" style="color:white">View Molecules</a><br /><br />';
		echo '<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />';
		echo '<a href="targets.php" style="color:white">View Targets</a><br /><br />';
		echo '</span>';
	}

?>
	</div>
	<div id="div_ad">
		<a href="http://web.chemdoodle.com"><img src="chemdoodleweb.png" /></a>
	</div>
</div>	
<div id="div_top">
<?php
		if($loggedin){
			echo '	<div id="div_notebook">';
			echo '<a href="notebook.php">My Notebook: '.substr_count($_SESSION['notebook_molids'],',').'</a>';
			echo '</div>';
			echo '<div id="div_login">';
			echo '<span id="span_loggedin">Logged in as '.$_SESSION['username'].' <a href="logout.php">(logout)</a></span>';
			echo '</div>';
		}else{		
			echo '<div id="div_login">';
			echo '<a href="registerpage.php" style="float:left;font-size:0.8em;">Create Account</a><br />';
			echo '<form id="login" method="post" action="login.php"	>';
			echo '<input type="text" value="username" name="enteredusername" id="enteredusername" onclick="clearuserbox();" size="7" />';
			echo '<input type="password" name="enteredpassword" value="password" id="enteredpassword" onclick="clearpasswordbox();" size="7" />';
			echo '<input type="submit" value="Log in"/>';
			echo '</form>';
 			if(isset($_GET['s'])){
                                if($_GET['s']=='badpw'){
                                        echo '<span style="font-size:0.8em;position:fixed;top:60px;left:960px;margin:0px;">';
                                        echo 'Invalid username/password.';
                                        echo '</span>';
                                }
                        }
			echo '</div>';
		}	
?>	
</div>
<div id="div_main">
<Br /><br />
Inhibitor Discovery, Design, and Quantification Database
</div>
</body>
</html>
