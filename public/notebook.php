<?php
	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}
	session_start();
	$loggedin = (isset($_SESSION['username']))? True:False;
	if(!$loggedin) returnhome();
    $notebookcount = count($_SESSION['notebook_molids']);
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>My Notebook</title>
	<link rel="stylesheet" href="reset.css" type="text/css" />
	<link rel="stylesheet" href="iddqd.css" type="text/css" />
	<script type="text/javascript" src="iddqd.js"></script>
</head>
<body>

<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
		<a href="index.php" style="color:#bbbbff">Home</a><br /><br />
		<a href="search.php" style="color:white">Search </a><br /><br />
		<a href="molecules.php" style="color:white">View Library</a><br /><br />
		<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
	    <a href="bounties.php" style="color:white">Bounties</a><br /><br />
		<a href="targets.php" style="color:white">Targets</a><br /><br />
		<a href="help.php" style="color:white">Help</a><br /><br />
	</span>
	</div>
</div>	
<div id="div_top">
	<div id="div_notebook">
		<a href="notebook.php">My Notebook: <?php echo $notebookcount; ?></a>
	</div>
	<div id="div_login">
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?><a href="logout.php">(logout)</a></span>
	</div>	
</div>
<div id="div_main">
	<br />
<?php
    if($notebookcount>0){
		echo '<span class="span_export">';
		echo '<a href="cgi-bin/export.py?export=pdf&userid='.$_SESSION['userid'].'&molids='.implode(',',$_SESSION['notebook_molids']).'">Export PDF</a>';
		echo '</span>';
		echo '<span class="span_export">';
		echo '<a href="cgi-bin/export.py?export=spreadsheet&userid='.$_SESSION['userid'].'&molids='.implode(',',$_SESSION['notebook_molids']).'" >Export Spreadsheet</a>';
		echo '</span>';	
		echo '<span class="span_export">';
		echo '<a href="cgi-bin/export.py?export=structures&userid='.$_SESSION['userid'].'&molids='.implode(',',$_SESSION['notebook_molids']).'" >Export Structures</a>';
		
        echo '</span>';
		echo '<table class="moleculetable">';
		echo '<tr class="moltr">';
		echo '<th class="molth moltdborderright">Structure</th>';
		echo '<th class="molth moltdborderright">Name</th>';
		echo '<th class="molth moltdborderright">MW</th>';
		echo '<th class="molth moltdborderright">Author</th>';
		echo '<th class="molth moltdborderright">Date Added</th>';
        echo '<th class="molth">Remove</th>';
		echo '</tr>';
		
	    $mollist=implode(',',$_SESSION['notebook_molids']);
		$qstr = 'SELECT a.molid,a.molname,a.dateadded,b.username,a.molweight from molecules a, users b where b.userid=a.authorid and a.molid in ('.$mollist.')';	
		$response=$dbconn->query($qstr);
		$count=0;
		foreach($response as $row){
			if($count%2==0){
				$tdcolor="";
			}else{
				$tdcolor="moltdcolor";
			}
			echo '<tr class="moltr">';
				echo '<td class="moltd moltdborderright '.$tdcolor.'">';
					echo '<a href="viewmolecule.php?molid='.$row['molid'].'"><img src="uploads/sketches/'.$row['molid'].'.jpg" style="height:60px"/></a>';
				echo '</td>';
				
				echo '<td class="moltd moltdborderright '.$tdcolor.'">';
					echo '<a href="viewmolecule.php?molid='.$row['molid'].'">'.htmlentities($row['molname']).'</a>';
				echo '</td>';
	
				echo '<td class="moltd moltdborderright '.$tdcolor.'">';
					echo $row['molweight'];
				echo '</td>';
	
				echo '<td class="moltd moltdborderright '.$tdcolor.'">';
					echo $row['username'];
				echo '</td>';
				echo '<td class="moltd moltdborderright '.$tdcolor.'">';
					echo parsetimestamp($row['dateadded']);
				echo '</td>';
                echo '<td class="moltd '.$tdcolor.'"><a href="removefromnotebook.php?molid='.$row['molid'].'&dest=nb"><img src="delete_icon.png"/></td>';
			echo '</tr>';	
            $count++;
        }
        echo '</table>';
	}	
    echo '<br /><div style="margin-top:100px;">';
    if($notebookcount==0){
        echo 'No notebook entries.';
    }
    echo '</div>';
?>
</div>
</body>
</html>

