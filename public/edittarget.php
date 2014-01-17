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
    $targetid = isset($_GET['targetid'])?(int)pg_escape_string($_GET['targetid']):-1;
	if($targetid < 0) returnhome(6);
    $q = $dbconn->prepare("SELECT nickname,fullname,targetclass,series FROM targets WHERE targetid = :num");
    $q->bindParam(":num",$targetid,PDO::PARAM_INT);
    $q->execute();
    if($q->rowCount() != 1) returnhome(7);
    $targetdata = $q->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Edit target</title>
    <link rel="stylesheet" href="reset.css" type="text/css" />
    <link rel="stylesheet" href="iddqd.css" type="text/css" />
    <script type="text/javascript" src="iddqd.js"></script>
</head>
<body>
<div id="div_holder">
<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
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
    <br/><br/>
    Edit target <?php echo $targetdata['nickname'];?>:
    <br />

    <span id="span_regspan">
    <?php 
        if(isset($_GET['status'])){
            if($_GET['status']=='nonickname'){
                echo "<span style=\"color:red;margin-right:50px;\">Nickname is required.</span><br/><br/>";
            }
            elseif($_GET['status']=='notauthor'){
                echo "<span style=\"color:red;margin-right:50px;\">You don't have permission to edit this target.</span><br/><br/>";
            }
        }
    ?>
     <form action="cgi-bin/edittarget.py" method="post" >
         <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />
         <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
         <input type="hidden" name="targetid" value="<?php echo $targetid;?>" />
         *Nickname: <input type="text" name="nickname" maxlength="25" required value="<?php echo $targetdata['nickname'];?>" /> <br/><br/>
         Full Name: <input type="text" name="fullname" maxlength="100" value="<?php echo $targetdata['fullname'];?>" /><br/><br/>
         Class: <input type="text" name="class"  maxlength="25" value="<?php echo $targetdata['targetclass'];?>" /><br/><br/>
         Series: <input type="text" name="series" maxlength="4" value="<?php echo $targetdata['series'];?>" /><br /><br/><br/>
         <input type="button" value="Cancel" onclick="window.location='viewtarget.php?targetid=<?php echo $targetid;?>';"/>
         <input type="submit" value="Submit" style="margin-left:45px;"/>
     </form>
    </span>


</div>
</div>
</body>
</html>
