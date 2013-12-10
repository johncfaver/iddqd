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
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Help</title>
<link rel="stylesheet" href="reset.css" type="text/css" />
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<link rel="stylesheet" href="help.css" type="text/css" />
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
		<a href="help.php" style="color:#bbbbff">Help</a><br /><br />
		</span>
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
<script type="text/javascript">
	var tabs = Array('searching','inserting','drawing','viewing','exporting');
	function selecthelp(t){
		for(var i=0;i<tabs.length;i++){
			var j = document.getElementById(tabs[i]);
			j.setAttribute("class","helptab nonlinks");
			j = document.getElementById('helpcontent_'+tabs[i]);
			j.style.display='none';
		}		
		var j = document.getElementById(t);
		j.setAttribute("class","helptab helptabselected nonlinks");
		j = document.getElementById('helpcontent_'+t);
		j.style.display='block';
	}

</script>
<div id="div_main">
	<h2>IDDQD-Help</h2><br />
	<div id="helpbar" >
		<div id="searching" onclick="selecthelp('searching');return false" class="helptab helptabselected nonlinks"><a href="#">Searching</a></div>
		<div id="inserting" onclick="selecthelp('inserting');return false" class="helptab nonlinks"><a href="#">Inserting</a></div>
		<div id="drawing" onclick="selecthelp('drawing');return false" class="helptab nonlinks"><a href="#">Drawing</a></div>
		<div id="viewing" onclick="selecthelp('viewing');return false" class="helptab nonlinks"><a href="#">Viewing</a></div>
		<div id="exporting" onclick="selecthelp('exporting');return false" class="helptab nonlinks"><a href="#">Exporting</a></div>
	</div>
	<div id="helpcontent_searching" class="help_content" style="display:block;"> 
		<br/>Search By:
		<ul>
			<li><b>Substructure</b></li>Draw the substructure query in the sketch window, and select "substructure" below (it is selected by default).<br/><br />
			<li><b>Similarity</b></li>Similar to substructure search, but select "similarity" below the sketch window. Results will be ordered by Tanimoto coefficient, which measures similarity. It ranges from 0 to 1, where 1 represents idential structures. Structures below a Tanimoto coefficient of 0.3 are omitted from the results. You can change this cutoff to 0.1 by adding &similaritythreshold=0.1 to the URL. <br /><br/>
			<li><b>Name</b></li>Search by Name, CAS, or IUPAC name. CAS and IUPAC are not always available, but most database entries have a Name consisting of a short target code and a number, e.g. JLJ0422.<br /><br />
			<li><b>Molecular weight</b></li>Search by molecular weight. Enter one value to view compounds closest to that molecular weight, or enter a range to view compounds within that molecular weight range.<br/><br/>
			<li><b>Target</b></li>Filter results by returning only compounds with binding data associated with the selected target.<br/><br/>
		</ul>

	</div>
	<div id="helpcontent_inserting" class="help_content"> 
		<br />To insert a new compound:
		<ul>
			<li><b>Sketch or upload the molecule.</b></li>
			Go to the "Add Molecules" page, and sketch the compound in the sketcher window. 
			Alternatively, you can upload a mol or SDF file in the field below the sketching window. 
			Keep in mind 2D structures work much better than 3D. 
			The sketcher will flatten 3D structures out into a 2D representation.<br/>
			<li><b>Enter the associated data.</b></li>
			Fill in name data in the available fields for Name, IUPAC, and CAS#. 
			None are required, but Name is required. As you select a target for the molecule, a name will be automatically suggested below the text box. Below the name fields are three tabs: <i>Binding, Properties, </i>and <i>Documents.</i> 
			Click the <i>Binding</i> tab to enter binding data. Select the target from the dropdown menu, and the binding data type from the second dropdown menu.
			Enter the value of the measurement in the text field, minding the units shown in the datatype dropdown menu. 	
			Finally, if there are any notes you would like to leave concerning this measurement, click the note icon at the right.
		 	A popup menu will appear, allowing you to enter your note. Notes may concern things such as who performed the experiment and when. 
			If you have additional binding data to enter, click the + icon. To delete the last item of data, click the - icon. 
			You can upload 5 items of data at a time.<br/>
			&emsp;Similar actions can be performed for other data in the <i>Properties</i> tab, where you can insert data like CC<sub>50</sub> and solubility.
			Finally, under the <i>Documents</i> tab, you can upload various documents related to the molecule. 
			Select a description from the dropdown menu, and click the button and select a file for upload. 
			You can submit notes for documents as well. <br/><br/>
			Click the "Submit" button to enter all of the data into the database or click "Clear all" to cancel and clear your input.<br/><br/>
		</ul>
		<hr/>To update data for an existing compound:
		<ul>
			<li><b>Search for the specific compound.</b></li>Search by name or some other identifier by using the search page.
			<li><b>Click on Edit in the molecule viewing page.</b></li>From the molecule's page, click the gray "Edit" tab at the far right. This will take you to the edit molecule page.
			<li><b>Enter or edit the new data.</b></li>Replace old data, delete old data, or insert new data by editing fields and using the +/- icons. 
		</ul>
	</div>
	<div id="helpcontent_drawing" class="help_content">
		<br />Drawing tips:<br/><br/>Drawing works a lot like ChemDraw, and is mostly straightforward. Here are some tips:
		<ul>
			<li><b>Use keyboard shortcuts.</b></li>Use numbers to change bond orders, e.g. 2 for double bonds. Use letters to change carbons to 'N','O', etc. Sometimes you'll have to cycle through elements, e.g. hit "s" twice for sulfur (the first s is silicon). Use ctrl+z and ctrl+y for undo and redo.<br/>
			<li><b>Use the mousedown feature.</b></li>To place new atoms in correct positions, click and hold down the mouse. You can postion the new atom to be at the intended angle.	<br/>
			<li><b>Use rigid rotations/translations.</b></li>Click the hand icon, and click and drag the background whitespace. This allows you to make whole molecule rigid translations. Hold the shift key while dragging to rotate the whole molecule.<br/>
			<li><b>Avoid zooming in and out.</b></li>This allows for more uniform images in the database. Avoid zooming unless the molecule doesn't fit into the window, then zoom out to show the entire molecule before submitting. 
			

	</div>
	<div id="helpcontent_viewing" class="help_content">
		<br />Select a molecule either from searching or browsing, and click its name or image to enter the molecule viewing page. 
			Here you'll find basic information at the top, and a selection of tabs for various data associated with the compound. 
		<ul><li>The <i>Binding</i> tab shows binding data, with each row consisting of data type, value, target, and comment. 
			If there is a comment associated with this data, you will see a <img src="info_icon.png" width=15/> icon. 
			Click it to see a popup with the note.</li>
			<li>The <i>Property</i> tab is similar to the binding tab, but for property data.</li>
			<li>The <i>Document</i> tab lists the various documents users have uploaded concerning the molecule. 
				These could be manuscripts, synethetic routes, spectra, or anything else relevant to the compound.</li>
			<li>The <i>Modeling</i> tab lists various files that were automatically generated by the server when the compound was entered into the database.
				Here you can find images, structures, and output from various programs.</li>
			<li>The <i>Comments</i> tab includes comments made by users about the molecule.</li>
		</ul>
		<br/><br/>
		There are two funcion tabs to the far right in gray. The first is for adding the current molecule to your notebook (see "exporting" data section). 
		The second is the edit button to edit the molecular data.	
	</div>
	<div id="helpcontent_exporting" class="help_content">
		<br/>Exporting data is done by adding compounds to "My Notebook". At a molecule viewing page, click the "Add to Notebook" function tab to add the 
			molecule to your notebook. The same tab is used to remove from your notebook. At any time, you can view your notebook by clicking the link 
			at the top right of the page next to your log in information. <br/><br/>
		&emsp;When viewing your notebook, you can export the selected molecules in various formats. For a PDF file with data for each compound in your notebook,
			 click "Export PDF". For a spreadsheet containing all of the available measurement data for the compounds, 
			click "Export Spreadsheet". For the 2D and 3D mol files as well as a compiled SDF file of your notebook, click "Export structures".
	</div>
</div>
</body>
</html>
