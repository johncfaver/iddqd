<?php
/*
    editmolecule.php
    Main page for editing molecule information.
    Sends data to cgi-bin/editmol.py which redirects
        to viewmolecule
    
*/
    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);    
    }catch(PDOException $e){
        //echo 'Database connection failed: '. $e->getMessage();
    }
    session_start();
    $loggedin = isset($_SESSION['username']);
    if(!$loggedin) returnhome(0);
    $thismolid = (isset($_GET['molid']))?(int)$_GET['molid']:-1;
    if($thismolid < 0 ) returnhome(4);
    //If there was an error from previous try because molecule name was empty
    $emptyname = (isset($_GET['emptyname']))?(int)$_GET['emptyname']:0;

    $q = $dbconn->prepare("SELECT molname,iupac,cas FROM molecules WHERE molid=:num");
    $q->bindParam(":num",$thismolid,PDO::PARAM_INT);
    $q->execute();    
    $r=$q->fetch(PDO::FETCH_ASSOC);
    $thismolname=htmlentities($r['molname']);
    $thiscas=htmlentities($r['cas']);
    $thisiupac=htmlentities($r['iupac']);

    //Read 2d mol file and load into ChemDoodleWeb
    $thismolfilelocation='uploads/structures/'.$thismolid.'.mol';
    if(file_exists($thismolfilelocation)){
        $handle=fopen($thismolfilelocation,'r');
        $thismolfilecontents='';
        while($line=fgets($handle)){
            $thismolfilecontents.=rtrim($line).'\n';
        }
        fclose($handle);
    }else{
        returnhome(5);
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
<div id="div_holder">

<!-- LEFT COLUMN -->
<div id="div_left">
    <div id="left_links">
        <span class="nonlinks">
        <?php if ($_SESSION['isadmin']) echo '<a href="admin.php" style="color:white">Administration</a><br/><br/>';?>
        <a href="index.php" style="color:white">Home</a><br /><br />
        <a href="search.php" style="color:white">Search </a><br /><br />
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
    <form id="datainput" enctype="multipart/form-data" action="../cgi-bin/editmol.py" method="post">
    <div id="div_datainput">
        <div id="div_datainput_inner">
            <span style="font-size:1.2em;">Edit Data:</span>
            <br /><br />
            <span style="font-size:0.8em;float:right;text-align:right;">
                Name:
                <input type="text" name="molname" id="molname" size=10 required 
                    maxlength="50" style="width:80px;margin-right:45px;" value="<?php echo $thismolname;?>" />
                IUPAC: 
                <input type="text" name="iupacname" id="iupacname" size=10 
                    maxlength="100" style="width:80px;float:right" value="<?php echo $thisiupac;?>" />    
            </span>
            <span style="margin-top:5px;font-size:0.8em;float:right;text-align:right;line-height:3em;">
                <span style="font-style:italic;color:#884444;margin-right:45px;" id="editmoleculeerror">
                    <?php if($emptyname) echo 'Name can not be empty.';?>
                </span>
                CAS#: <input type="text" name="cas" id="cas" size=10 
                            maxlength="20" style="width:80px;float:right" value="<?php echo $thiscas;?>" />
                <br />
            </span>
            <br /><br />

            <div id="div_dataentry">
                <div id="div_tab_datatype_bindingdata" class="div_tab_datatype nonlinks" onclick="switchinputdatatab('bindingdata');return false">
                    <a href="#">Binding</a>
                </div>
                <div id="div_tab_datatype_propertydata" class="div_tab_datatype nonlinks" onclick="switchinputdatatab('propertydata');return false">
                    <a href="#">Properties</a>
                </div>
                <div id="div_tab_datatype_docdata" class="div_tab_datatype nonlinks" onclick="switchinputdatatab('docdata');return false">
                    <a href="#">Documents</a>
                </div>
    
<!-- BINDING DATA -->
                <div id="div_input_datatype_bindingdata">
                    <div id="bindingdatainputlines" class="div_datainputlines"></div>
                    <div id="button_morebindingdata" class="button_plusdata">
                        <a href="#"><img src="add_icon.png" class="nonlinks" onclick="morebindingdata();return false" /></a>
                    </div>
                    <div id="button_lessbindingdata" class="button_minusdata">
                        <a href="#"><img src="minus_icon.png" class="nonlinks" onclick="lessbindingdata();return false" /></a>
                    </div>
                </div>
            
<!-- PROPERTY DATA -->
                <div id="div_input_datatype_propertydata">
                    <div id="propertydatainputlines" class="div_datainputlines"></div>
                    <div id="button_morepropertydata" class="button_plusdata">
                        <a href="#"><img src="add_icon.png" class="nonlinks" onclick="morepropertydata();return false" /></a>
                    </div>
                    <div id="button_lesspropertydata" class="button_minusdata">
                        <a href="#"><img src="minus_icon.png" class="nonlinks" onclick="lesspropertydata();return false" /></a>
                    </div>
                </div>

<!-- DOCUMENT DATA -->
                <div id="div_input_datatype_docdata">
                    <div id="docdatainputlines" class="div_datainputlines"></div>
                    <div id="button_moredocdata" class="button_plusdata">
                        <a href="#"><img src="add_icon.png" class="nonlinks" onclick="moredocdata();return false" /></a>
                    </div>
                    <div id="button_lessdocdata" class="button_minusdata">
                        <a href="#"><img src="minus_icon.png" class="nonlinks" onclick="lessdocdata();return false" /></a>
                    </div>
                </div>
            </div> <!-- close data_entry -->

        <input type="hidden" name="moltext" id="moltext" value="default" />
        <input type="hidden" name="molfig" id="molfig" value="default" />
        <input type="hidden" value="<?php echo $thismolid;?>" name="molid" />
        <input type="hidden" value="<?php echo $_SESSION['userid'];?>" name="userid" />
        <input type="hidden" value="<?php echo $_SESSION['token'];?>" name="token" />
        <input type="hidden" name="oldbindingdataids" id="input_oldbindingdataids" value=""/>
        <input type="hidden" name="oldpropertydataids" id="input_oldpropertydataids" value=""/>
        <input type="hidden" name="olddocdataids" id="input_olddocdataids" value=""/>
        <input type="hidden" name="oldcommentids" id="input_oldcommentids" value=""/>
        
        <input type="submit" id="button_moledit" value="Submit" onclick="getmolecule();" />

        </form>
        
        <form action="viewmolecule.php" method="GET">
            <input type="hidden" value="<?php echo $thismolid;?>" name="molid" />
            <input type="submit" value="Cancel" id="button_cancelmoledit" />
        </form>
                    
        </div><!-- close data_input_inner-->
    </div><!-- close data_input-->
</div><!-- close main-->

<div id="div_shade_window">
</div>
<div id="div_deletecheck" class="div_notespopup">
    <form action="../cgi-bin/deletedata.py" method="post">
        <input type="hidden" name="molid" value="<?php echo $thismolid; ?>" />
        <input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>" />
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
        <input type="hidden" name="deletedataid" id="deletedataid" value="" />
        <input type="hidden" name="deletedocdatatype" id="deletedocdatatype" value="" />
        <span class="span_popup_main_text">
            Are you sure you want to delete this data?
        </span>
        <input type="submit" value="Delete" class="button_popup button_popup_left" />
        <input type="button" value="Cancel" class="button_popup button_popup_right"  onclick="closedeletecheck();"/>
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
/*Populate existing data to be viewed/edited.
  Grab info from database and filesystem, then put in javascript arrays.
*/

//Get target data from database.
        $q=$dbconn->query("SELECT nickname,targetid FROM targets ORDER BY targetid");
        foreach($q as $target){
            echo 'targetnames.push("'.$target['nickname'].'");';
            echo 'targetids.push("'.$target['targetid'].'");';
        }

//Get data for this molid from database        
        $q=$dbconn->prepare("SELECT DISTINCT 
                                d.targetid,
                                d.value,
                                d.datatype,
                                dt.class,
                                c.datacomment,
                                d.moldataid,
                                c.datacommentid
                                FROM 
                                    moldata d 
                                    LEFT JOIN datatypes dt on dt.datatypeid=d.datatype
                                    LEFT JOIN datacomments c ON d.moldataid = c.dataid 
                                WHERE d.molid=:num 
                                ORDER BY d.datatype");
        $q->bindParam(":num",$thismolid,PDO::PARAM_INT);
        $q->execute();
        while($row=$q->fetch(PDO::FETCH_ASSOC)){
            $comment = htmlentities($row['datacomment']);
            if($row['class']==1){
                if($row['datacommentid']){
                    echo 'populatebindingdata('.$row['moldataid'].','.$row['datatype'].','.$row['targetid'].','.$row['value'].','.$row['datacommentid'].',\''.str_replace("\r\n","<br />",addslashes($comment)).'\');';
                }else{
                    echo 'populatebindingdata('.$row['moldataid'].','.$row['datatype'].','.$row['targetid'].','.$row['value'].',0,\'\');';
                }
            }else if($row['class']==2){
                if($row['datacommentid']){
                    echo 'populatepropertydata('.$row['moldataid'].','.$row['datatype'].','.$row['value'].','.$row['datacommentid'].',\''.str_replace("\r\n","<br />",addslashes($comment)).'\');';
                }else{
                    echo 'populatepropertydata('.$row['moldataid'].','.$row['datatype'].','.$row['value'].',0,\'\');';
                }
            }else if($row['class']==3){
                $tarray = glob('uploads/documents/'.$thismolid.'_'.$row['datatype'].'_'.$row['moldataid'].'*');
                if (count($tarray)==1){
                    $filename = $tarray[0];
                }else{
                    $filename = '';
                }
                if($row['datacommentid']){
                    echo 'populatedocdata(\''.addslashes($filename).'\','.$row['moldataid'].','.$row['datatype'].','.$row['datacommentid'].',\''.str_replace("\r\n","\\n",addslashes($comment)).'\');';
                }else{
                    echo 'populatedocdata(\''.addslashes($filename).'\','.$row['moldataid'].','.$row['datatype'].',0,\'\');';
                }
            }
        }

?>
</script>
</div>
</body>
</html>
