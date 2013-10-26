<?php
	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}
	session_start();
	$loggedin = isset($_SESSION['username']);
	if(!$loggedin) returnhome();
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />
	<link rel="stylesheet" href="reset.css" type="text/css" />
	<link rel="stylesheet" href="ChemDoodleWeb/install/ChemDoodleWeb.css" type="text/css" />
	<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb-libs.js"></script>
	<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb.js"></script>
	<link rel="stylesheet" href="ChemDoodleWeb/install/sketcher/jquery-ui-1.8.7.custom.css" type="text/css" />
	<script type="text/javascript" src="ChemDoodleWeb/install/sketcher/jquery-ui-1.8.7.custom.min.js"></script>
	<script type="text/javascript" src="ChemDoodleWeb/install/sketcher/ChemDoodleWeb-sketcher.js"></script>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>Molecule Search</title>
	<link rel="stylesheet" href="iddqd.css" type="text/css" />
	<script type="text/javascript" src="iddqd.js"></script>
</head>
<body>

<div id="div_left">
	<div id="left_links">
	<span class="nonlinks" >
		<a href="index.php" style="color:white">Home</a><br /><br />
		<a href="search.php" style="color:#bbbbff">Search</a><br /><br />
		<a href="molecules.php" style="color:white">View Library</a><br /><br />
		<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
		<a href="bounties.php" style="color:white">Bounties</a><br /><br />
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
		<a href="notebook.php">My Notebook: <?php echo count($_SESSION['notebook_molids']); ?></a>
	</div>
	<div id="div_login">
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?><a href="logout.php">(logout)</a></span>
	</div>	
</div>
<div id="div_main">

	<div id="div_draw">
		<script>
		ChemDoodle.ELEMENT['H'].jmolColor = 'black';
		ChemDoodle.ELEMENT['S'].jmolColor = '#B9A130';
		// canvasID, width, height, icondirectory,touch,cloud
		var sketcher = new ChemDoodle.SketcherCanvas('sketcher', 500, 300, 'ChemDoodleWeb/install/sketcher/icons/', ChemDoodle.featureDetection.supports_touch(), false);
		sketcher.specs.atoms_displayTerminalCarbonLabels_2D = true;
		sketcher.specs.atoms_useJMOLColors = true;
		sketcher.specs.bonds_clearOverlaps_2D = true;
		sketcher.specs.bonds_overlapClearWidth_2D = 2;
		sketcher.repaint();
		</script>
	</div>
	
	<div id="div_datainput">
	<div id="div_datainput_inner" style="position:absolute;text-align:right;top:100px;float:right;">
		<form action="performsearch.php" method="post" >
			Name:<input type="text" id="query_molname" name="query_molname" size=15 />
				<span style="font-size:0.7em;"><br />e.g. "JLJ0294", "propan-2-amine", "JL%4"</span>
			<br /><br /><br />
			Molecular Weight:<input type="text" id="query_molweight" name="query_molweight" size=8 />
			<span style="font-size:0.7em;"><br />e.g. "340", "200-250"</span>
			<br /><br />
			Target: <select name="query_targetid" id="query_targetid">
			<option value="0">-</option>
<?php
 		$query=$dbconn->query("SELECT nickname,targetid FROM targets ORDER BY nickname");
        foreach($query as $target){
			echo '<option value="'.$target['targetid'].'">';
			echo $target['nickname'].'</option>';
		}
?>	
			</select>
		<br /><br/>
		Number of results: <input type="text" id="query_nummol" name="nummol" value=0 size=2 />
            	<span style="font-size:0.7em;"><br />0: no limit</span><br/><br/>


		<div id="search_radio" style="position:absolute;border:0px solid red;left:-450px;top:325px;width:350px">
			<span style="float:left;"><input type="radio" name="searchtype" value="substructure" checked>Substructure</span>
			<span style="float:right;"><input type="radio" name="searchtype" value="similarity">Similarity</span>
		</div>
		<input type="hidden" name="moltext" id="moltext" value="" />
		<input type="hidden" name="molfig" id="molfig" value="" />
		<input type="submit" value="Search" style="margin-top:50px;" onclick="getmolecule();" />
		</form>	
	</div>
	</div>

</div>
</body>
</html>


