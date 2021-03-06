<?php
	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		//echo 'Database connection failed: '. $e->getMessage();
	}
	session_start();
	$loggedin = isset($_SESSION['username']);
	if(!$loggedin) returnhome(0);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Add a new target</title>
<link rel="stylesheet" href="reset.css" type="text/css" />
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<script type="text/javascript" src="iddqd.js"></script>
</head>
<body>
<div id="div_holder">
<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
        <?php if ($_SESSION['isadmin']) echo '<a href="admin.php" style="color:white">Administration</a><br/><br/>';?>
		<a href="index.php" style="color:white">Home</a><br /><br />
		<a href="search.php" style="color:white">Search </a><br /><br />
		<a href="molecules.php" style="color:white">View Library</a><br /><br />
		<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
	    <a href="bounties.php" style="color:white">Bounties</a><br /><br />
	    <a href="targets.php" style="color:#bbbbff">Targets</a><br /><br />
		<a href="help.php" style="color:white">Help</a><br /><br />
	</span>
	</div>
</div>	
<div id="div_top">
	<div id="div_notebook">
		<a href="notebook.php">My Notebook: <?php echo count($_SESSION['notebook_molids']); ?></a>
	</div>
	<div id="div_login">
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?> <a href="logout.php">(logout)</a></span>
	</div>	
</div>
<div id="div_main">
<span style="font-size:1.3em;position:absolute;left:360px;top:50px;">Add a new target:</span>
<span id="span_regspan">
    <?php 
        if(isset($_GET['status'])){
            if($_GET['status']=='nonickname'){
                echo '<span style="color:red;margin-right:50px;">Short Name is required.</span><br/><br/>';
            }
        }
    ?>
    <form action="cgi-bin/addtarget.py" method="post" >
	    <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
	    *Short Name: <input type="text" name="nickname" maxlength="25" required /> <br/><br/>
	    Full Name: <input type="text" name="fullname" maxlength="25" /><br/><br/>
	    Class: <input type="text" name="class"  maxlength="25" /><br/><br/>
	    Series: <input type="text" name="series" maxlength="4" /><br /><br/><br/>
        <input type="submit" value="Add Target" />
    </form>
</span>

</div>
</div>
</body>
</html>
