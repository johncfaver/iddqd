<?php

	require('/home/faver/bin/cred.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}
	session_start();
	$loggedin = isset($_SESSION['username']);
	if(!$loggedin) returnhome();	
	
	$q = $dbconn->query("SELECT COUNT(molid) FROM molecules");
	$r = $q->fetch();
	$ntotmol=$r['count'];

?>
<!DOCTYPE html>
<html>
<head>
<title>Molecules</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="reset.css" type="text/css" />
<link rel="stylesheet" href="iddqd.css" type="text/css" />
</head>
<body>

<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
		<a href="index.php" style="color:white">Home</a><br /><br />
		<a href="search.php" style="color:white">Search</a> <br /><br />
		<a href="molecules.php" style="color:#bbbbff">View Molecules</a><br /><br />
		<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
		<a href="targets.php" style="color:white">View Targets</a><br /><br />
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
<?php
	$nummol=(isset($_GET['nummol']))?(int)$_GET['nummol']:10;
	$molstart=(isset($_GET['molstart']))?(int)$_GET['molstart']:0;
	$sortby=(isset($_GET['sortby']))?pg_escape_string($_GET['sortby']):'dateadded';
	$sortdir=(isset($_GET['sortdir']))?(int)$_GET['sortdir']:1;
	
	if($molstart>=$nummol){
		echo '<div id="div_molecules_prev"><span class="nonlinks"><a href="molecules.php?molstart='.($molstart-$nummol).'&sortby='.$sortby.'&sortdir='.$sortdir.'"> << previous </a></span></div>';
	}
	if($molstart+$nummol<$ntotmol){
		echo '<div id="div_molecules_next"><span class="nonlinks"><a href="molecules.php?molstart='.($molstart+$nummol).'&sortby='.$sortby.'&sortdir='.$sortdir.'"> next>></a></span></div>';
	}
?>
<table id="moleculetable" >
	<tr class="moltr">
		<th class="molth">Structure </th>
		<?php
			echo '<th class="molth"><a href="molecules.php?sortby=molname';
			if($sortby=='molname' && $sortdir==0) echo '&sortdir=1';
			if($sortby=='molname' && $sortdir) echo '&sortdir=0';
			echo '">Name</a></th>';

			echo '<th class="molth"><a href="molecules.php?sortby=molweight';
			if($sortby=='molweight' && $sortdir==0) echo '&sortdir=1';
			if($sortby=='molweight' && $sortdir) echo '&sortdir=0';
			echo'">MW</a></th>';
	
			echo '<th class="molth"><a href="molecules.php?sortby=username';
			if($sortby=='username' && $sortdir==0) echo '&sortdir=1';
			if($sortby=='username' && $sortdir) echo '&sortdir=0';
			echo'">Author</a></th>';

			echo '<th class="molth"><a href="molecules.php?sortby=dateadded';
			if($sortby=='dateadded' && $sortdir==0) echo '&sortdir=1';
			if($sortby=='dateadded' && $sortdir) echo '&sortdir=0';
			echo '">Date Added</a></tr>';
		?>
	</tr>
<?php
	$qstr = 'SELECT a.molid,a.molname,a.dateadded,b.username,a.molweight from molecules a, users b where b.userid=a.authorid order by '.$sortby;	
	if($sortdir){
		$qstr.=' DESC';
	}
	$qstr.=' limit :num1 offset :num2';
	$q = $dbconn->prepare($qstr); 
	$q->bindParam(":num1",$nummol,PDO::PARAM_INT);
	$q->bindParam(":num2",$molstart,PDO::PARAM_INT);
	$q->execute();
	$response=$q->fetchAll(PDO::FETCH_ASSOC);
	$count=1;
	foreach($response as $entry){
		if($count%2==0){
			$tdcolor="moltd2";
		}else{
			$tdcolor="moltd1";
		}
		echo '<tr class="moltr">';
			echo '<td class="'.$tdcolor.'">';
				echo '<a href="viewmolecule.php?molid='.$entry['molid'].'"><img src="uploads/sketches/'.$entry['molid'].'.jpg" style="height:60px"/></a>';
			echo '</td>';
			
			echo '<td class="'.$tdcolor.'">';
				echo '<a href="viewmolecule.php?molid='.$entry['molid'].'">'.$entry['molname'].'</a>';
			echo '</td>';

			echo '<td class="'.$tdcolor.'">';
				echo $entry['molweight'];
			echo '</td>';

			echo '<td class="'.$tdcolor.'">';
				echo $entry['username'];
			echo '</td>';
			echo '<td class="'.$tdcolor.'">';
				echo parsetimestamp($entry['dateadded']);
			echo '</td>';
		echo '</tr>';	
		$count++;
	}	
?>	
</table>

</div>
</body>
</html>
