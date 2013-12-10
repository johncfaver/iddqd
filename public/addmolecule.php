<?php
/*
    addmolecule.php
    Main page for drawing molecules and entering new data into the database.
    Form is sent to ../cgi-bin/uploadmol.py which eventually redirects user 
    back to this page.

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
    
    //If there was a previous failure due to no molecule name given
    $emptyname = (isset($_GET['emptyname']))?(int)$_GET['emptyname']:0;

    //If a mol/sdf file has been uploaded
    $fileupload = isset($_FILES['sdffileupload']);
    if($fileupload){
        $thismolfilename=substr($_FILES['sdffileupload']['name'],0,-4);
        if(file_exists($_FILES['sdffileupload']['tmp_name'])){
            $handle = fopen($_FILES['sdffileupload']['tmp_name'],'r');
            $thismolfilecontents='';
            while($line=fgets($handle)){
                $thismolfilecontents.=rtrim($line).'\n';
            }
            fclose($handle);
        }
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
    <script type="text/javascript">
        var datafields = new Array();
        var targetnames= new Array();
        var targetids= new Array();
        var num_bindingdata = 0;
        var num_propertydata = 0;
        var num_docdata = 0;
        //targetnames.push("&nbsp;&nbsp;&nbsp;[---Select target---]");
        //targetids.push("-1");

<?php
        $query=$dbconn->query("SELECT nickname,targetid FROM targets ORDER BY targetid");
        foreach($query as $target){
            echo 'targetnames.push("'.$target['nickname'].'");';
            echo 'targetids.push("'.$target['targetid'].'");';
        }
?>
 
    </script>
    <title>Add a Molecule</title>
</head>
<body onload="morebindingdata();morepropertydata();moredocdata();">

<div id="div_left">
    <div id="left_links">
        <span class="nonlinks">
        <a href="index.php" style="color:white">Home</a><br /><br />
        <a href="search.php" style="color:white">Search</a> <br /><br />
        <a href="molecules.php" style="color:white ">View Library</a><br /><br />
        <a href="addmolecule.php" style="color:#bbbbff">Add Molecules</a><br /><br />
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
            echo '<form action="addmolecule.php" method="post" enctype="multipart/form-data">';
            echo '<input type="file" name="sdffileupload" />';
            echo '<input type="submit" value="Upload SDF"/>';
            echo '</form>';
        }
?>

    </div>


    <div id="div_datainput">
        <form id="datainput" enctype="multipart/form-data" action="../cgi-bin/uploadmol.py" method="post">
       
        <div id="div_molnotes">Notes about this molecule:<br />
            <textarea id="molnotes" name="molnotes" rows="4" cols="35" ></textarea>
        </div>

        <div id="div_datainput_inner"><span style="font-size:1.2em;">Input Data:</span>
            <br /><br />
            <span style="font-size:0.8em;float:right;text-align:right;">
                Name:
                <input type="text" name="molname" id="molname" size=10 required maxlength="50" 
                    style="width:80px;margin-right:45px;" <?php if($fileupload) echo 'value="'.$thismolfilename.'"'; ?>/>
                IUPAC: <input type="text" name="iupacname" id="iupacname" style="width:80px;float:right" size=10 maxlength="100" value="" />
            </span>
            <span style="margin-top:5px;font-size:0.8em;float:right;text-align:right;line-height:3em;">
                <span style="font-style:italic;color:#884444;margin-right:45px;" id="molnameRecommendation"><?php if($emptyname) echo 'Name can not be empty.';?></span>
                CAS#: <input type="text" name="cas" id="cas" size=10 maxlength="20" style="width:80px;float:right" value="" />
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
                    <br />
                    <div id="bindingdatainputlines">    
                    </div>
                    <br/>
                    <div id="button_morebindingdata" style="display:inline">
                        <a href="#"><img src="add_icon.png" style="nonlinks" onclick="morebindingdata();return false" /></a>
                    </div>
                    <div id="button_lessbindingdata" style="display:none;">
                        <a href="#"><img src="minus_icon.png" style="nonlinks" onclick="lessbindingdata();return false" /></a>
                    </div>
                </div>
        
<!-- PROPERTY DATA -->
                <div id="div_input_datatype_propertydata">
                    <br />
                    <div id="propertydatainputlines">
                    </div>
                    <br />
                    <div id="button_morepropertydata" style="display:inline;">
                        <a href="#"><img src="add_icon.png" class="nonlinks" onclick="morepropertydata();return false" /></a>
                    </div>
                    <div id="button_lesspropertydata" style="display:none;">
                        <a href="#"><img src="minus_icon.png" class="nonlinks" onclick="lesspropertydata();return false" /></a>
                    </div>
                </div>

<!-- DOCUMENT DATA -->
                <div id="div_input_datatype_docdata">
                    <br />
                    <div id="docdatainputlines">
                    </div>
                    <br />
                    <div id="button_moredocdata" style="display:inline;">
                        <a href="#"><img src="add_icon.png" class="nonlinks" onclick="moredocdata();return false" /></a>
                    </div>
                    <div id="button_lessdocdata" style="display:none;">
                        <a href="#"><img src="minus_icon.png" class="nonlinks" onclick="lessdocdata();return false" /></a>
                    </div>
                </div>

            </div>
            <br /><br />
            <input type="hidden" name="moltext" id="moltext" value="default" />
            <input type="hidden" name="molfig" id="molfig" value="default" />
            <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" /> 
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
            <input type="submit" id="button_moledit" value="Submit" onclick="getmolecule();" />
            </form>
            <form action="addmolecule.php">
                <input type="submit" value="Clear all" id="button_cancelmoledit"/>
            </form>
        </div>
    </div>
</div>
</body>
</html>
