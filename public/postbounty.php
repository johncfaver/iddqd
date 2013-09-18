<?php
	require('../private/cred.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}
	session_start();

	$loggedin = isset($_SESSION['username']);
	if(!$loggedin) returnhome();

	$fileupload = isset($_FILES['sdffileupload']);
	if($fileupload){
		$thismolfilename=substr($_FILES['sdffileupload']['name'],0,-4);
		$handle = fopen($_FILES['sdffileupload']['tmp_name'],'r');
		$thismolfilecontents='';
		while($line=fgets($handle)){
			$thismolfilecontents.=rtrim($line).'\n';
		}
		fclose($handle);
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />
	<link rel="stylesheet" href="reset.css" type="text/css" />
	<link rel="stylesheet" href="ChemDoodleWeb/install/ChemDoodleWeb.css" type="text/css" />
	<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb-libs.js"></script>
	<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb.js"></script>
	<link rel="stylesheet" href="ChemDoodleWeb/install/sketcher/jquery-ui-1.8.7.custom.css" type="text/css" />
	<script type="text/javascript" src="ChemDoodleWeb/install/sketcher/jquery-ui-1.8.7.custom.min.js"></script>
	<script type="text/javascript" src="ChemDoodleWeb/install/sketcher/ChemDoodleWeb-sketcher.js"></script>
	<script type="text/javascript" src="iddqd.js"></script>
	<title>Post a Bounty</title>
	<link rel="stylesheet" href="iddqd.css" type="text/css" />
    <link rel="stylesheet" href="bounty.css" type="text/css" />
</head>
<body>

<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
		<a href="index.php" style="color:white">Home</a><br /><br />
		<a href="search.php" style="color:white">Search</a> <br /><br />
		<a href="molecules.php" style="color:white ">View Library</a><br /><br />
		<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
		<a href="bounties.php" style="color:#bbbbff">Bounties</a><br /><br />
        <a href="targets.php" style="color:white">Targets</a><br /><br />
		<a href="help.php" style="color:white">Help</a><br /><br />
		</span>
	</div>
	<div id="div_ad">
		<a href="http://web.chemdoodle.com"><img src="chemdoodleweb.png" /></a>
	</div>
</div>	
<div id="div_top">
	<div id="div_notebook">
		<a href="notebook.php">My Notebook: <?php echo count($_SESSION['notebook_molids'])+count($_SESSION['notebook_bountyids']); ?></a>
	</div>
	<div id="div_login">
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?> <a href="logout.php">(logout)</a></span>
	</div>	
</div>
<div id="div_main">
	<div id="div_draw">
		<script>
		ChemDoodle.ELEMENT['H'].jmolColor = 'black';
		ChemDoodle.ELEMENT['S'].jmolColor = '#B9A130';
		// arguments: canvasID, width, height, icondirectory, touch, cloud
		var sketcher = new ChemDoodle.SketcherCanvas('sketcher', 500, 300, 'ChemDoodleWeb/install/sketcher/icons/', ChemDoodle.featureDetection.supports_touch(), false);
		sketcher.specs.atoms_displayTerminalCarbonLabels_2D = true;
		sketcher.specs.atoms_useJMOLColors = true;
		sketcher.specs.bonds_clearOverlaps_2D = true;
		sketcher.specs.bonds_overlapClearWidth_2D = 2;
<?php
		if($fileupload){
			echo 'sketcher.molecule=ChemDoodle.readMOL("'.$thismolfilecontents.'");';
			echo 'sketcher.molecule.check();';
			echo 'sketcher.center();';
		}
?>
		sketcher.repaint();
		</script>
<?php
		if(!$fileupload){
			echo '<form action="postbounty.php" method="post" enctype="multipart/form-data">';
			echo '<input type="file" name="sdffileupload" />';
			echo '<input type="submit" value="Upload SDF"/>';
			echo '</form>';
		}
?>

	</div>


	<div id="div_datainput">
	<form id="datainput" enctype="multipart/form-data" action="cgi-bin/uploadbounty.py?username=<?php echo $_SESSION['username'];?>&userid=<?php echo $_SESSION['userid'];?>" method="post">
	<div id="div_postbounty_notes">Notes about this bounty:<br /><textarea id="molnotes" name="molnotes" rows="5" cols="40" ></textarea></div>


	
		<div id="div_input_datatype_bindingdata">
			<br />
                <div id="target_select">
                    Target:&nbsp;&nbsp;&nbsp;<select id="select_targetid" name="select_targetid">
<?php
 		$query=$dbconn->query("SELECT nickname,targetid FROM targets ORDER BY targetid");
        foreach($query as $target){
	        echo '<option value="'.$target['targetid'].'">'.$target['nickname'].'</option>';
	    }
?>
                    </select>
			</div>
				  
	
		</div>
			<input type="hidden" name="moltext" id="moltext" value="default" />
			<input type="hidden" name="molfig" id="molfig" value="default" />
			<br /><br />
			<input type="submit" id="button_bountysubmit" value="Submit" onclick="getmolecule();" />
			</form>
			<form action="postbounty.php"><input type="submit" value="Clear all" id="button_cancelbountysubmit"/></form>
	
</div>


</body>
</html>
