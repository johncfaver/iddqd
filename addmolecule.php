<?php

	require('/home/faver/bin/cred.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Connection failed: '. $e->getMessage();
	}
	session_start();

	$loggedin = (isset($_SESSION['username']))?True:False;
	if(!$loggedin) returnhome();

	if($_FILES['sdffileupload']){
		$fileupload=True;
		$thismolfilename=substr($_FILES['sdffileupload']['name'],0,-4);
		$handle = fopen($_FILES['sdffileupload']['tmp_name'],'r');
		$thismolfilecontents='';
		while($line=fgets($handle)){
			$thismolfilecontents.=rtrim($line).'\n';
		}
		fclose($handle);
	}else{
		$fileupload=False;
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<link rel="stylesheet" href="ChemDoodleWeb/install/ChemDoodleWeb.css" type="text/css" />
<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb-libs.js"></script>
<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb.js"></script>
<link rel="stylesheet" href="ChemDoodleWeb/install/sketcher/jquery-ui-1.8.7.custom.css" type="text/css" />
<script type="text/javascript" src="ChemDoodleWeb/install/sketcher/jquery-ui-1.8.7.custom.min.js"></script>
<script type="text/javascript" src="ChemDoodleWeb/install/sketcher/ChemDoodleWeb-sketcher.js"></script>
<script type="text/javascript" src="iddqd.js"></script>

<script type="text/javascript">
	var datafields = new Array();
</script>

<title>Add a Molecule</title>

<link rel="stylesheet" href="iddqd.css" type="text/css" />
</head>
<body>

<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
		<a href="index.php" style="color:white">Home</a><br /><br />
		<a href="search.php" style="color:white">Search</a> <br /><br />
		<a href="molecules.php" style="color:white ">View Molecules</a><br /><br />
		<a href="addmolecule.php" style="color:#bbbbff">Add Molecules</a><br /><br />
		<a href="targets.php" style="color:white">View Targets</a><br /><br />
		</span>
	</div>
	<div id="div_ad">
		<a href="http://web.chemdoodle.com"><img src="chemdoodleweb.png" /></a>
	</div>
</div>	
<div id="div_top">
	<div id="div_login">
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?> <a href="logout.php">(logout)</a></span>
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
			echo '<form action="addmolecule.php" method="post" enctype="multipart/form-data">';
			echo '<input type="file" name="sdffileupload" />';
			echo '<input type="submit" value="Upload SDF"/>';
			echo '</form>';
		}
?>

	</div>


	<div id="div_datainput">
	<form id="datainput" enctype="multipart/form-data" action="cgi-bin/uploadmol.py?username=<?php echo $_SESSION['username'];?>&userid=<?php echo $_SESSION['userid'];?>" method="post">
	<div id="div_molnotes">Notes about this molecule:<br /><textarea id="molnotes" name="molnotes" rows="4" cols="35" ></textarea></div>


		<div id="div_datainput_inner"><span style="font-size:1.2em;">Input Data:</span><br />
				<br /><span style="font-size:0.8em;float:right;text-align:right;">
				WLJID:
				<input type="text" name="molname" id="molname" size=10 style="width:80px;" <?php if($thismolfilename) echo 'value="'.$thismolfilename.'"'; ?>/>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				IUPAC: <input type="text" name="iupacname" id="iupacname" style="width:80px;float:right" size=10 value="" />	
				<br />CAS#: <input type="text" name="cas" id="cas" size=10 style="width:80px;float:right" value="" /><br />
				</span>
			<br /><br />
		<div id="div_dataentry">
			<span class="nonlinks">
			<div id="div_tab_datatype_bindingdata" onclick="switchinputdatatab('bindingdata');">
				<a href="#">Binding</a>
			</div>
			<div id="div_tab_datatype_propertydata" onclick="switchinputdatatab('propertydata');">
				<a href="#">Properties</a>
			</div>
			<div id="div_tab_datatype_docdata" onclick="switchinputdatatab('docdata');">
				<a href="#">Documents</a>
			</div>
			</span>
	
<!-- BINDING DATA -->
		<div id="div_input_datatype_bindingdata">
			<br />
			<div id="bindingdatainputlines">	
<script type="text/javascript">
	var targetnames= new Array();
	var targetids= new Array();
	var num_bindingdata = 0;
<?php
 		$query=$dbconn->query("SELECT nickname,targetid FROM targets ORDER BY targetid");
                foreach($query as $target){
			echo 'targetnames.push("'.$target['nickname'].'");';
			echo 'targetids.push("'.$target['targetid'].'");';
		}
?>
	morebindingdata();

</script>
			</div><br/><div id="button_morebindingdata" style="display:inline"><span style="nonlink"><a href="#"><img src="add_icon.png" onclick="morebindingdata();" /></a></span></div>
				   <div id="button_lessbindingdata" style="display:none;"><span style="nonlink"><a href="#"><img src="minus_icon.png" onclick="lessbindingdata();" /></a></span></div>
		</div>
		
<!-- PROPERTY DATA -->
		<div id="div_input_datatype_propertydata">
			<br />
			<div id="propertydatainputlines">
<script type="text/javascript">
	var num_propertydata = 0;
	morepropertydata();
</script>
			</div><br />
			<div id="button_morepropertydata" style="display:inline;">
				<span class="nonlinks">
					<a href="#"><img src="add_icon.png" onclick="morepropertydata();" /></a>
				</span>
			</div>
			<div id="button_lesspropertydata" style="display:none;">
				<span class="nonlinks">
					<a href="#"><img src="minus_icon.png" onclick="lesspropertydata();" /></a>
				</span>
			</div>
		</div>

<!-- DOCUMENT DATA -->
		<div id="div_input_datatype_docdata">
			<br />
			<div id="docdatainputlines">
				<script type="text/javascript">
					var num_docdata = 0;
					moredocdata();
				</script>
			</div><br />
			<div id="button_moredocdata" style="display:inline;">
				<span class="nonlinks">
					<a href="#"><img src="add_icon.png" onclick="moredocdata();" /></a>
				</span>
			</div>
			<div id="button_lessdocdata" style="display:none;">
				<span class="nonlinks">
					<a href="#"><img src="minus_icon.png" onclick="lessdocdata();" /></a>
				</span>
			</div>
			
		</div>


		</div>
			<input type="hidden" name="moltext" id="moltext" value="default" />
			<input type="hidden" name="molfig" id="molfig" value="default" />
			<br /><br />
			<input type="submit" id="button_moledit" value="Submit" onclick="getmolecule();" />
			</form>
			<form action="addmolecule.php"><input type="submit" value="Clear all" id="button_cancelmoledit"/></form>
		</div>
	</div>
	
</div>


</body>
</html>
