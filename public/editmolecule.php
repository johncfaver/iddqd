<?php
/*
    Main page for editing molecule information.
*/
    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);    
    }catch(PDOException $e){
        echo 'Database connection failed: '. $e->getMessage();
    }
    session_start();

    $loggedin = isset($_SESSION['username']);
    $thismolid = (isset($_GET['molid']))?(int)$_GET['molid']:-1;
    if(!$loggedin or $thismolid==-1 ) returnhome();
    $emptyname = (isset($_GET['emptyname']))?(int)$_GET['emptyname']:0;

    $q = $dbconn->prepare("SELECT molname,iupac,cas FROM molecules WHERE molid=:num");
    $q->bindParam(":num",$thismolid,PDO::PARAM_INT);
    $q->execute();    
    $r=$q->fetch();
    $thismolname=htmlentities($r['molname']);
    $thiscas=htmlentities($r['cas']);
    $thisiupac=htmlentities($r['iupac']);

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
                <input type="text" name="molname" id="molname" size=10 required maxlength="50" style="width:80px;margin-right:45px;" value="<?php echo $thismolname;?>" />
                IUPAC: <input type="text" name="iupacname" id="iupacname" size=10 maxlength="100" style="width:80px;float:right" value="<?php echo $thisiupac;?>" />    
            </span>
            <span style="margin-top:5px;font-size:0.8em;float:right;text-align:right;line-height:3em;">
                <span style="font-style:italic;color:#884444;margin-right:45px;" id="editmoleculeerror"><?php if($emptyname) echo 'Name can not be empty.';?></span>
                CAS#: <input type="text" name="cas" id="cas" size=10 maxlength="20" style="width:80px;float:right" value="<?php echo $thiscas;?>" /><br />
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
                    <br/><br/>
                    <div id="bindingdatainputlines"></div>
                    <br/><br/>
                    <div id="button_morebindingdata" style="display:inline">
                    <span style="nonlink">
                        <a href="#"><img src="add_icon.png" onclick="morebindingdata();return false" /></a>
                    </span>
                    </div>
                    <div id="button_lessbindingdata" style="display:none;">
                        <span style="nonlink">
                            <a href="#"><img src="minus_icon.png" onclick="lessbindingdata();return false" /></a>
                        </span>
                    </div>
                </div>
            
<!-- PROPERTY DATA -->
                <div id="div_input_datatype_propertydata">
                    <br/><br/>
                    <div id="propertydatainputlines"></div>
                    <br/><br/>
                    <div id="button_morepropertydata" style="display:inline;">
                        <span class="nonlinks">
                            <a href="#"><img src="add_icon.png" onclick="morepropertydata();return false" /></a>
                        </span>
                    </div>
                    <div id="button_lesspropertydata" style="display:none;">
                        <span class="nonlinks">
                            <a href="#"><img src="minus_icon.png" onclick="lesspropertydata();return false" /></a>
                        </span>
                    </div>
                </div>

<!-- DOCUMENT DATA -->
                <div id="div_input_datatype_docdata">
                    <br/><br/>
                    <div id="docdatainputlines"></div>
                    <br/><br/>
                    <div id="button_moredocdata" style="display:inline;">
                        <span class="nonlinks">
                            <a href="#"><img src="add_icon.png" onclick="moredocdata();return false" /></a>
                        </span>
                    </div>
                    <div id="button_lessdocdata" style="display:none;">
                        <span class="nonlinks">
                            <a href="#"><img src="minus_icon.png" onclick="lessdocdata();return false" /></a>
                        </span>
                    </div>
                </div>
            </div> <!-- close data_entry -->

        <input type="hidden" name="moltext" id="moltext" value="default" />
        <input type="hidden" name="molfig" id="molfig" value="default" />
        <input type="hidden" value="<?php echo $thismolid;?>" name="molid" />
        <input type="hidden" value="<?php echo $_SESSION['userid'];?>" name="userid" />
        <input type="hidden" name="oldbindingdataids" id="input_oldbindingdataids" value=""/>
        <input type="hidden" name="oldpropertydataids" id="input_oldpropertydataids" value=""/>
        <input type="hidden" name="olddocdataids" id="input_olddocdataids" value=""/>
        <input type="hidden" name="oldcommentids" id="input_oldcommentids" value=""/>
        
        <input type="submit" id="button_moledit" value="Submit" onclick="getmolecule();" />

        </form>
        
        <form action="viewmolecule.php" method="get">
            <input type="hidden" value="<?php echo $thismolid;?>" name="molid" />
            <input type="submit" value="Cancel" id="button_cancelmoledit" />
        </form>
                    
        </div><!-- close data_input_inner-->
    </div><!-- close data_input-->
</div><!-- close main-->

<div id="div_shade_window"></div>
<div id="div_deletecheck" class="div_notespopup">
    <form action="../cgi-bin/deletedata.py" method="post">
        <input type="hidden" name="molid" value="<?php echo $thismolid; ?>" />
        <input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>" />
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
         $q=$dbconn->query("SELECT nickname,targetid FROM targets ORDER BY targetid");
        foreach($q as $target){
            echo "\n".'targetnames.push("'.$target['nickname'].'");';
            echo "\n".'targetids.push("'.$target['targetid'].'");';
        }
        
        $q=$dbconn->prepare("SELECT distinct targetid,value,datatype,datacomment,moldataid,datacommentid from moldata left join datacomments on moldata.moldataid=datacomments.dataid where molid=:num order by datatype");
        $q->bindParam(":num",$thismolid,PDO::PARAM_INT);
        $q->execute();
        while($row=$q->fetch()){
            $comment = htmlentities($row['datacomment']);
            if(in_array($row['datatype'],$bindingdataids)){
                if($row['datacommentid']){
                    echo "\n".'populatebindingdata('.$row['moldataid'].','.$row['datatype'].','.$row['targetid'].','.$row['value'].','.$row['datacommentid'].',\''.str_replace("\r\n","<br />",addslashes($comment)).'\');';
                }else{
                    echo "\n".'populatebindingdata('.$row['moldataid'].','.$row['datatype'].','.$row['targetid'].','.$row['value'].',0,\'\');';
                }
            }else if(in_array($row['datatype'],$propertydataids)){
                if($row['datacommentid']){
                    echo "\n".'populatepropertydata('.$row['moldataid'].','.$row['datatype'].','.$row['value'].','.$row['datacommentid'].',\''.str_replace("\r\n","<br />",addslashes($comment)).'\');';
                }else{
                    echo "\n".'populatepropertydata('.$row['moldataid'].','.$row['datatype'].','.$row['value'].',0,\'\');';
                }
            }else if(in_array($row['datatype'],$docdataids)){
                $filename=exec('ls uploads/documents/'.$thismolid.'_'.$row['datatype'].'_'.$row['moldataid'].'_*');
                if($row['datacommentid']){
                    echo "\n".'populatedocdata(\''.$filename.'\','.$row['moldataid'].','.$row['datatype'].','.$row['datacommentid'].',\''.str_replace("\r\n","\\n",addslashes($comment)).'\');';
                }else{
                    echo "\n".'populatedocdata(\''.$filename.'\','.$row['moldataid'].','.$row['datatype'].',0,\'\');';
                }
            }
        }

?>
</script>

</body>
</html>
