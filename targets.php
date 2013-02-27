<?php
	require('/home/faver/bin/cred.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Connection failed: '. $e->getMessage();
	}
	session_start();
	$loggedin = (isset($_SESSION['username']))? True:False;
	if(!$loggedin) returnhome();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Targets</title>
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<script type="text/javascript" src="iddqd.js"></script>
</head>
<body>

<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
		<a href="index.php" style="color:white">Home</a><br /><br />
		<a href="search.php" style="color:white">Search</a> <br /><br />
		<a href="molecules.php" style="color:white">View Molecules</a><br /><br />
		<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
		<a href="targets.php" style="color:#bbbbff">View Targets</a><br /><br />
		<a href="help.php" style="color:white">Help</a><br /><br />
	</span>
	</div>
	<div id="div_ad">
		<a href="http://web.chemdoodle.com"><img src="chemdoodleweb.png" /></a>
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

<table id="moleculetable" >
	<tr class="moltr">
		<th class="molth">Name</th>	
		<th class="molth">Nickname</th>
		<th class="molth">Target Class</th>	
		<th class="molth">Inhibitors</th>
	</tr>
<?php
		$icount=0;
		$q=$dbconn->query("select t.targetid,t.fullname,t.nickname,t.targetclass,count(m.molid) from targets t left join moldata m on t.targetid=m.targetid group by t.targetid order by count desc;");
		foreach($q as $row){
			$color=($icount%2==0)?'moltd1':'moltd2';
			echo '<tr class="moltr">';
			echo '	<td class="'.$color.'">';
			echo	$row['fullname']; 	
			echo '	</td>';
			echo '	<td class="'.$color.'">';
			echo	$row['nickname']; 	
			echo '	</td>';
			echo '	<td class="'.$color.'">';
			echo    $row['targetclass'];
			echo '	</td>';
			echo '	<td class="'.$color.'">';
			echo    $row['count'];
			echo '	</td>';
			echo '</tr>';
			$icount+=1;
		}	
?>	
	</table>
	
</table>

</div>
</body>
</html>
