<?php
/*
	Main page for editing molecule information.
*/
	require('/home/faver/bin/cred.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		echo 'Database connection failed: '. $e->getMessage();
	}
	session_start();

	$loggedin = isset($_SESSION['username']);
	$thismolid = (isset($_GET['molid']))?(int)$_GET['molid']:-1;
	if(!$loggedin or $thismolid==-1 ) returnhome();

	$q = $dbconn->prepare("SELECT molname,iupac,cas FROM molecules WHERE molid=:num");
	$q->bindParam(":num",$thismolid,PDO::PARAM_INT);
	$q->execute();	
	$r=$q->fetch();
	$thismolname=$r['molname'];
	$thiscas=$r['cas'];
	$thisiupac=$r['iupac'];

	//Read mol file and load into ChemDoodleWeb
	$thismolfilelocation='uploads/structures/'.$thismolid.'.mol';
	if(file_exists($thismolfilelocation)){
		$handle=fopen($thismolfilelocation,'r');
		$thismolfilecontents='';
		while($line=fgets($handle)){
			$thismolfilecontents.=rtrim($line).'\n';
		}
		fclose($handle);
	}else{
		returnhome();
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
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<title>Editing <?php echo $thismolname;?></title>
</head>
<body>

<!-- LEFT COLUMN -->
<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
		<a href="index.php" style="color:white">Home</a><br /><br />
		<a href="search.php" style="color:white">Search </a><br /><br />
		<a href="molecules.php" style="color:white">View Molecules</a><br /><br />
		<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
		<a href="targets.php" style="color:white">View Targets</a><br /><br />
		<a href="help.php" style="color:white">Help</a><br /><br />
	</span>
	</div>
	<div id="div_ad">
		<a href="http://web.chemdoodle.com"><img src="chemdoodleweb.png" /></a>
	</div>
</div>	

<!-- TOP ROW -->
<div id="div_top">
	<div id="div_notebook">
		<a href="notebook.php">My Notebook: <?php echo count($_SESSION['notebook_molids']); ?></a>
	</div>
	<div id="div_login">
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?> <a href="logout.php">(logout)</a></span>
	</div>	
</div>

<!-- MAIN DIV -->
<div id="div_main">

<!-- CHEMDOODLE WINDOW -->
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
		sketcher.molecule=ChemDoodle.readMOL("<?php echo $thismolfilecontents;?>");
		sketcher.molecule.check();
		sketcher.center();
		sketcher.repaint();
		</script>
	</div>

<!-- DATA INPUT -->
<?php
	echo '<form id="datainput" enctype="multipart/form-data" action="cgi-bin/editmol.py?username='.urlencode($_SESSION['username']).'&userid='.$_SESSION['userid'].'" method="post">';
?>	
	<div id="div_datainput">
		<div id="div_datainput_inner">
			<span style="font-size:1.2em;">Edit Data:</span><br />	
			<br /><span style="font-size:0.8em;float:right;text-align:right;">
			WLJID:
			<input type="text" name="molname" id="molname" size=10 style="width:80px" value="<?php echo $thismolname;?>" />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			IUPAC: <input type="text" name="iupacname" id="iupacname" size=10 style="width:80px;float:right" value="<?php echo $thisiupac;?>" />	
			<br />CAS#: <input type="text" name="cas" id="cas" size=10 style="width:80px;float:right" value="<?php echo $thiscas;?>" /><br />
			</span><br /><br />

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
			<div id="div_input_datatype_bindingdata"><br />
				<div id="bindingdatainputlines"></div><br/>

				<div id="button_morebindingdata" style="display:inline">
					<span style="nonlink"><a href="#"><img src="add_icon.png" onclick="morebindingdata();" /></a></span>
				</div>
				<div id="button_lessbindingdata" style="display:none;">
					<span style="nonlink"><a href="#"><img src="minus_icon.png" onclick="lessbindingdata();" /></a></span>
				</div>
			</div>
			
<!-- PROPERTY DATA -->
			<div id="div_input_datatype_propertydata"><br />
				<div id="propertydatainputlines"></div><br />
	
				<div id="button_morepropertydata" style="display:inline;">
					<span class="nonlinks"><a href="#"><img src="add_icon.png" onclick="morepropertydata();" /></a></span>
				</div>
				<div id="button_lesspropertydata" style="display:none;">
					<span class="nonlinks"><a href="#"><img src="minus_icon.png" onclick="lesspropertydata();" /></a></span>
				</div>
			</div>

<!-- DOCUMENT DATA -->
			<div id="div_input_datatype_docdata"><br />
				<div id="docdatainputlines">
				</div><br />
		
				<div id="button_moredocdata" style="display:inline;">
					<span class="nonlinks"><a href="#"><img src="add_icon.png" onclick="moredocdata();" /></a></span>
				</div>
				<div id="button_lessdocdata" style="display:none;">
					<span class="nonlinks"><a href="#"><img src="minus_icon.png" onclick="lessdocdata();" /></a></span>
				</div>

			</div>

		</div>
		<input type="hidden" name="moltext" id="moltext" value="default" />
		<input type="hidden" name="molfig" id="molfig" value="default" />
		<input type="hidden" value="<?php echo $thismolid;?>" name="molid" />
		<input type="submit" id="button_moledit" value="Submit" onclick="getmolecule();" />
		<input type="hidden" name="oldbindingdataids" id="input_oldbindingdataids" value=""/>
		<input type="hidden" name="oldpropertydataids" id="input_oldpropertydataids" value=""/>
		<input type="hidden" name="olddocdataids" id="input_olddocdataids" value=""/>
		<input type="hidden" name="oldcommentids" id="input_oldcommentids" value=""/>
		</form>
		
		<form action="viewmolecule.php" method="get">
			<input type="hidden" value="<?php echo $thismolid;?>" name="molid" />
			<input type="submit" value="Cancel" id="button_cancelmoledit" />
		</form>

		
		</div>
	</div>
</div>

<div id="div_deletecheck" class="div_notespopup" style="text-align:center;">
	<form action="cgi-bin/deletedata.py?molid=<?php echo $thismolid; ?>&userid=<?php echo $_SESSION['userid'];?>" method="post">
		<input type="hidden" name="deletedataid" id="deletedataid" value="" />
		<input type="hidden" name="deletedocdatatype" id="deletedocdatatype" value="" />
		<span style="position:absolute;top:50px;left:45px">
			Are you sure you want to delete this data?
		</span><br /><br />
		<input type="submit" value="Yes" style="position:absolute;width:100px;height:30px;left:50px;top:200px;"/>
		<input type="button" value="Cancel" style="position:absolute;width:100px;height:30px;right:50px;top:200px;" onclick="closedeletecheck();"/>
	</form>
</div>
<!-- POPULATE DATA -->
<script type="text/javascript">
	var targetnames= new Array();
	var targetids= new Array();
	var num_bindingdata = 0;
	var num_propertydata = 0;
	var num_docdata = 0;

<?php
 		$q=$dbconn->query("SELECT nickname,targetid FROM targets ORDER BY targetid");
                foreach($q as $target){
			echo "\n".'targetnames.push("'.$target['nickname'].'");';
			echo "\n".'targetids.push("'.$target['targetid'].'");';
		}
		
		$q=$dbconn->prepare("SELECT distinct targetid,value,datatype,datacomment,moldataid,datacommentid from moldata left join datacomments on moldata.moldataid=datacomments.dataid where molid=:num order by datatype");
		$q->bindParam(":num",$thismolid,PDO::PARAM_INT);
		$q->execute();
		while($row=$q->fetch()){
			if(in_array($row['datatype'],$bindingdataids)){
				if($row['datacommentid']){
					echo "\n".'populatebindingdata('.$row['moldataid'].','.$row['datatype'].','.$row['targetid'].','.$row['value'].','.$row['datacommentid'].',\''.str_replace("\r\n","<br />",$row['datacomment']).'\');';
				}else{
					echo "\n".'populatebindingdata('.$row['moldataid'].','.$row['datatype'].','.$row['targetid'].','.$row['value'].',0,\'\');';
				}
			}else if(in_array($row['datatype'],$propertydataids)){
				if($row['datacommentid']){
					echo "\n".'populatepropertydata('.$row['moldataid'].','.$row['datatype'].','.$row['value'].','.$row['datacommentid'].',\''.str_replace("\r\n","<br />",$row['datacomment']).'\');';
				}else{
					echo "\n".'populatepropertydata('.$row['moldataid'].','.$row['datatype'].','.$row['value'].',0,\'\');';
				}
			}else if(in_array($row['datatype'],$docdataids)){
				$filename=exec('ls uploads/documents/'.$thismolid.'_'.$row['datatype'].'_'.$row['moldataid'].'_*');
				if($row['datacommentid']){
					echo "\n".'populatedocdata(\''.$filename.'\','.$row['moldataid'].','.$row['datatype'].','.$row['datacommentid'].',\''.str_replace("\r\n","<br />",$row['datacomment']).'\');';
				}else{
					echo "\n".'populatedocdata(\''.$filename.'\','.$row['moldataid'].','.$row['datatype'].',0,\'\');';
				}
			}
		}

?>
</script>

</body>
</html>
