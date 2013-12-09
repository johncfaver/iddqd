<?php
	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		//echo 'Database connection failed: '. $e->getMessage();
	}
	session_start();
	$loggedin = isset($_SESSION['username']);
	if(!$loggedin) returnhome();
    $notebookcount = count($_SESSION['notebook_molids']); //number of molids in notebook
    $molids=implode(',',$_SESSION['notebook_molids']);    //comma-separated string of molids in notebook
    $molstart = isset($_GET['molstart'])?(int)pg_escape_string($_GET['molstart']):0;
	$nummol=(isset($_GET['nummol']))?(int)pg_escape_string($_GET['nummol']):8;
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
		<a href="index.php" style="color:white">Home</a><br /><br />
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
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?><a href="logout.php"> (logout)</a></span>
	</div>	
</div>
<div id="div_main">
	<br />
<?php
    if($notebookcount>0){
		echo '<span class="span_export">
		        <form action="../cgi-bin/export.py" method="POST">
                    <input type="hidden" name="export" value="pdf" />
                    <input type="hidden" name="molids" value="'.$molids.'" />
                    <input type="hidden" name="userid" value="'.$_SESSION['userid'].'" />
                    <input type="submit" value="Export PDF" />
                </form>
		      </span>';
		echo '<span class="span_export">
		        <form action="../cgi-bin/export.py" method="POST">
                    <input type="hidden" name="export" value="spreadsheet" />
                    <input type="hidden" name="molids" value="'.$molids.'" />
                    <input type="hidden" name="userid" value="'.$_SESSION['userid'].'" />
                    <input type="submit" value="Export CSV" />
                </form>
		      </span>';
		echo '<span class="span_export">
		        <form action="../cgi-bin/export.py" method="POST">
                    <input type="hidden" name="export" value="structures" />
                    <input type="hidden" name="molids" value="'.$molids.'" />
                    <input type="hidden" name="userid" value="'.$_SESSION['userid'].'" />
                    <input type="submit" value="Export Structures" />
                </form>
		      </span>';
    
	    if($molstart>=$nummol){
		    echo '<div id="div_molecules_prev" class="nonlinks" style="margin-top:50px;"><a href="notebook.php?molstart='.($molstart-$nummol).'"> << previous </a></div>';
	    }
	    if($molstart+$nummol<$notebookcount){
		    echo '<div id="div_molecules_next" class="nonlinks" style="margin-top:50px;"><a href="notebook.php?molstart='.($molstart+$nummol).'"> next >> </a></div>';
	    }
		echo '<table class="moleculetable" style="margin-top:30px;">';
		echo '<tr class="moltr">';
		echo '<th class="molth moltdborderright">Structure</th>';
		echo '<th class="molth moltdborderright">Name</th>';
		echo '<th class="molth moltdborderright">MW</th>';
		echo '<th class="molth moltdborderright">Author</th>';
		echo '<th class="molth moltdborderright">Date Added</th>';
        echo '<th class="molth"><a href="removefromnotebook.php?all=1&dest=nb" style="color:red" title="Remove All">Remove</a></th>';
		echo '</tr>';
	
        $qmarks = str_repeat('?,',$notebookcount-1)."?"; // Need one ? for each molid in notebook for SQL statement preparation
        $q = $dbconn->prepare("SELECT a.molid,a.molname,a.dateadded,b.username,a.molweight from molecules a, users b where b.userid=a.authorid and a.molid in 
                                (".$qmarks.") limit ".$nummol." offset ".$molstart);
        $q->execute($_SESSION['notebook_molids']);
		$count=0;
        while($row = $q->fetch(PDO::FETCH_ASSOC)){
			if($count%2==0){
				$tdcolor="";
			}else{
				$tdcolor="moltdcolor";
			}
			echo '<tr class="moltr">
				    <td class="moltd moltdborderright '.$tdcolor.'">
				        <a href="viewmolecule.php?molid='.$row['molid'].'">
                            <img src="uploads/sketches/'.$row['molid'].'.jpg" style="height:60px"/>
                        </a>
				    </td>
				    <td class="moltd moltdborderright '.$tdcolor.'">
					    <a href="viewmolecule.php?molid='.$row['molid'].'">'.htmlentities($row['molname']).'</a>
			        </td>
				    <td class="moltd moltdborderright '.$tdcolor.'">
                        '.$row['molweight'].'
			        </td>
				    <td class="moltd moltdborderright '.$tdcolor.'">
					   '.$row['username'].'
				    </td>
				    <td class="moltd moltdborderright '.$tdcolor.'">
				        '.parsetimestamp($row['dateadded']).'
				    </td>
                    <td class="moltd '.$tdcolor.'">
                        <a href="removefromnotebook.php?molid='.$row['molid'].'&dest=nb">
                            <img src="delete_icon.png" title="Remove From Notebook"/>
                        </a>
                    </td>
			    </tr>';	
            $count++;
        }
        echo '</table>';
	}	
    if($notebookcount==0){
        echo '<br /><div style="margin-top:100px;">';
        echo 'No notebook entries.';
        echo '</div>';
    }
   ?>
</div>
</body>
</html>

