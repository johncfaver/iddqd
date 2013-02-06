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
<title>My Notebook</title>
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<script type="text/javascript" src="iddqd.js"></script>
</head>
<body>

<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
		<a href="index.php" style="color:#bbbbff">Home</a><br /><br />
		<a href="search.php" style="color:white">Search </a><br /><br />
		<a href="molecules.php" style="color:white">View Molecules</a><br /><br />
		<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
		<a href="targets.php" style="color:white">View Targets</a><br /><br />
		</span>
	</div>
</div>	
<div id="div_top">
	<div id="div_notebook">
		<a href="notebook.php">My Notebook: <?php echo substr_count($_SESSION['notebook_molids'],','); ?></a>
	</div>
	<div id="div_login">
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?><a href="logout.php">(logout)</a></span>
	</div>	
</div>
<div id="div_main">
	<br />
	<span style="width:300px;float:left">
		<a href="cgi-bin/export.py?export=pdf&userid=<?php echo $_SESSION['userid'];?>&molids=<?php echo $_SESSION['notebook_molids'];?>" >Export PDF</a>
	</span>	
	<span style="width:300px;float:left">
		<a href="cgi-bin/export.py?export=spreadsheet&userid=<?php echo $_SESSION['userid'];?>&molids=<?php echo $_SESSION['notebook_molids'];?>" >Export Spreadsheet</a>
	</span>	
	<span style="width:300px;float:left">	
		<a href="cgi-bin/export.py?export=structures&userid=<?php echo $_SESSION['userid'];?>&molids=<?php echo $_SESSION['notebook_molids'];?>" >Export Structures</a>
	</span>	

	<table id="moleculetable">
		<tr class="moltr">
			<th class="molth">Structure</th>
			<th class="molth">Name</th>
			<th class="molth">MW</th>
			<th class="molth">Author</th>
			<th class="molth">Date Added</th>
		</tr>
<?php
	$mollist=rtrim($_SESSION['notebook_molids'],',');
	$qstr = 'SELECT a.molid,a.molname,a.dateadded,b.username,a.molweight from molecules a, users b where b.userid=a.authorid and a.molid in ('.$mollist.')';	
	$response=$dbconn->query($qstr);
	$count=1;
	foreach($response as $row){
		if($count%2==0){
			$tdcolor="moltd2";
		}else{
			$tdcolor="moltd1";
		}
		echo '<tr class="moltr">';
			echo '<td class="'.$tdcolor.'">';
				echo '<a href="viewmolecule.php?molid='.$row['molid'].'"><img src="uploads/sketches/'.$row['molid'].'.jpg" style="height:60px"/></a>';
			echo '</td>';
			
			echo '<td class="'.$tdcolor.'">';
				echo '<a href="viewmolecule.php?molid='.$row['molid'].'">'.$row['molname'].'</a>';
			echo '</td>';

			echo '<td class="'.$tdcolor.'">';
				echo $row['molweight'];
			echo '</td>';

			echo '<td class="'.$tdcolor.'">';
				echo $row['username'];
			echo '</td>';
			echo '<td class="'.$tdcolor.'">';
				echo parsetimestamp($row['dateadded']);
			echo '</td>';
		echo '</tr>';	
		$count++;
	}	
?>
	</table>


</div>
</body>
</html>
