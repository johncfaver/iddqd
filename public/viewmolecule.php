<?php
    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);    
    }catch(PDOException $e){
        //echo 'Database connection failed: '. $e->getMessage();
    }

    session_start();
    $loggedin = isset($_SESSION['username']) and isset($_SESSION['userid']);
    $thismolid = isset($_GET['molid'])?(int)pg_escape_string($_GET['molid']):-1;

    if(!$loggedin or $thismolid<0) returnhome();

    $q = $dbconn->prepare("SELECT 
                            m.molname,
                            m.authorid,
                            u.username,
                            m.dateadded,
                            m.molweight,
                            m.molformula,
                            m.iupac,
                            m.cas 
                           FROM molecules m LEFT JOIN users u ON m.authorid=u.userid
                           WHERE m.molid=:num
                          ");
    $q->bindParam(":num",$thismolid,PDO::PARAM_INT);
    $q->execute();
    if($q->rowCount() != 1) returnhome();
    $moldata=$q->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="reset.css" type="text/css" />
<link rel="stylesheet" href="ChemDoodleWeb/install/ChemDoodleWeb.css" type="text/css">
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<link rel="stylesheet" href="viewmolecule.css" type="text/css" />
<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb-libs.js"></script>
<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb.js"></script>
<script type="text/javascript" src="iddqd.js"></script>
<script type="text/javascript" src="viewmolecule.js"></script>
<title><?php echo htmlentities($moldata['molname'], ENT_QUOTES); ?></title>
</head>
<body>

<div id="div_datapopup"></div>
<div id="div_shade_window"></div>
<div id="div_deletecheck" class="div_notespopup" >
    <form action="../cgi-bin/deletemolecule.py" method="post">
        <input type="hidden" name="molid" value="<?php echo $thismolid;?>" />
        <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />
        <span class="span_popup_main_text">
            Are you sure you want to delete this molecule?
        </span>
        <input type="submit" value="Delete" class="button_popup button_popup_left" />
		<input type="button" value="Cancel" class="button_popup button_popup_right"  onclick="closedeletecheck();"/>
    </form>
</div>

<div id="div_left">
    <div id="left_links">
        <span class="nonlinks">
	        <a href="index.php" style="color:white">Home</a><br /><br />
	        <a href="search.php" style="color:white">Search</a> <br /><br />
	        <a href="molecules.php" style="color:#bbbbff">View Library</a><br /><br />
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
        <span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?> <a href="logout.php">(logout)</a></span>
    </div>    
</div>
<div id="div_main">
    <div id="div_molimg">
        <script type="text/javascript">
            //initialize component and set visual specifications
            var viewerCanvas = new ChemDoodle.ViewerCanvas('viewerCanvas', 400, 200);
            //the width of the bonds should be .6 pixels
            viewerCanvas.specs.bonds_width_2D = 1.0;
            //the spacing between higher order bond lines should be 18% of the length of the bond
            viewerCanvas.specs.bonds_saturationWidth_2D = .18;
            //the hashed wedge spacing should be 2.5 pixels
            viewerCanvas.specs.bonds_hashSpacing_2D = 2.5;
            //the atom label font size should be 10
            viewerCanvas.specs.atoms_font_size_2D = 12;
            //we define a cascade of acceptable font families
            //if Helvetica is not found, Arial will be used
            viewerCanvas.specs.atoms_font_families_2D = ["Helvetica", "Arial", "sans-serif"];
            //display carbons labels if they are terminal
            viewerCanvas.specs.atoms_displayTerminalCarbonLabels_2D = true;
            var molfile='<?php
                        $fileContents=file_get_contents('uploads/structures/'.$thismolid.'.mol');
                            if($fileContents){
                                echo str_replace(array("\r\n", "\n", "\r", "'"), array("\\n", "\\n", "\\n", "\\'"), $fileContents);
                            }?>';
            var thismol = ChemDoodle.readMOL(molfile);
            //the bond lengths should be 14.4 pixels in ACS Document 1996
            thismol.scaleToAverageBondLength(20.0);
            viewerCanvas.loadMolecule(thismol);
            var t = document.getElementById('viewerCanvas');
            t.setAttribute('style','border:0px');
        </script>
    </div>
    <div id="div_molentry">
        <span style="font-size:2.0em;"><?php echo htmlentities($moldata['molname'], ENT_QUOTES);?></span>
        <br />Added by: <?php echo $moldata['username'];?> on <?php echo parsetimestamp($moldata['dateadded']);?>
<?php
            if($moldata['username']==$_SESSION['username']){
                echo '&nbsp;<a href="#" onclick="deletecheck();">(delete)</a>';
            }
?>

        <table id="table_molinfo">
            <tr class="molecules_tr">
                <td class="molecules_td molecules_tdl molecules_tdr">MW</td>
                <td class="molecules_td molecules_tdr molecules_tdl"><?php echo $moldata['molweight'];?></td>
                <td class="molecules_td  molecules_tdr">Formula</td>
                <td class="molecules_td molecules_tdr molecules_tdl"><?php echo parseformula($moldata['molformula']);?></td>
            </tr>
            <tr class="molecules_tr">
                <td class="molecules_td molecules_tdl molecules_tdr">IUPAC</td>
                <td class="molecules_td molecules_tdr molecules_tdl"><?php echo $moldata['iupac'];?></td>
                <td class="molecules_td molecules_tdr">CAS #</td>
                <td class="molecules_td molecules_tdr molecules_tdl"><?php echo $moldata['cas'];?></td>
            </tr>
        </table>    
    </div>

    <div id="div_moldata">

<?php
        $q = $dbconn->prepare("SELECT DISTINCT username,nickname,value,datacomment,moldata.dateadded,moldata.datatype,moldata.moldataid,type,units from moldata left join targets on moldata.targetid=targets.targetid left join datacomments on moldata.moldataid=datacomments.dataid join users on users.userid=moldata.authorid join datatypes on datatypes.datatypeid=datatype where molid=:num order by type");
        $q->bindParam(":num",$thismolid,PDO::PARAM_INT);
        $q->execute();
        $response=$q->fetchAll();
?>
    <span class="nonlinks">
    <a href="#"><div id="div_tabbindingdata" class="datatab datatabopen" onclick="switchdatadiv('bindingdata');">Binding</div></a>
    <a href="#"><div id="div_tabpropertydata" class="datatab" onclick="switchdatadiv('propertydata');">Properties</div></a>
    <a href="#"><div id="div_tabdocdata" class="datatab" onclick="switchdatadiv('docdata');">Documents</div></a>
    <a href="#"><div id="div_tabmodelingdata" class="datatab" onclick="switchdatadiv('modelingdata');">Modeling</div></a>
    <a href="#"><div id="div_tabcommentdata" class="datatab" onclick="switchdatadiv('commentdata');">Comments</div></a>
    <a href="editmolecule.php?molid=<?php echo $thismolid;?>"><div id="div_editdata" class="datatab" >Edit</div></a>
<?php
    if(!in_array($thismolid,$_SESSION['notebook_molids'])){
        echo '<a href="addtonotebook.php?molid='.$thismolid.'"><div id="div_addtonotebook" class="datatab" >Add to Notebook</div></a>';
    }else{
        echo '<a href="removefromnotebook.php?molid='.$thismolid.'"><div id="div_addtonotebook" class="datatab" >Remove from Notebook</div></a>';
    }
?>
    </span>

    <div id="div_bindingdata" class="div_data">
        <table id="bindingtable" class="viewmolecule_datatable">
            <tr><th class="molecules_th">Data Type</th><th class="molecules_th">Value</th><th class="molecules_th">Target</th><th class="molecules_th">Notes</th></tr>
            <?php
                $count=0;
                foreach($response as $r){
                    if(!in_array(strval($r['datatype']),$bindingdataids))continue;
                    $count++;
                    echo '<tr><td class="molecules_td molecules_tdl">';    
                        echo $datatypefromid[strval($r['datatype'])].'</td><td class="molecules_td molecules_tdr">';
                        echo $r['value'].' '.$r['units'].'</td><td class="molecules_td">';
                        echo $r['nickname'].'</td>';
                        if($r['datacomment']){
                            echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\'';
                            echo $r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\''.str_replace("\r\n",'<br />',htmlentities($r['datacomment'])).'\');">';
                            echo '<img src="info_icon.png" height=15 title="Notes Available" />';
                        }else{
                            echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\'';
                            echo $r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\'No Notes.\');">';
                        }
                        echo '</td></tr>';
                }
                if($count==0){    
                    echo '<tr><td></td><td><br /><br />No data.</td></tr>';
                }
            ?>    
        </table>
    </div>
    <div id="div_propertydata" class="div_data" style="display:none">
        <table id="propertytable" class="viewmolecule_datatable">
            <tr><th class="molecules_th">Data Type</th><th class="molecules_th">Value</th><th class="molecules_th">Notes</th></tr>
            <?php
                $count=0;
                foreach($response as $r){
                    if(!in_array(strval($r['datatype']),$propertydataids))continue;
                    $count++;
                    echo '<tr><td class="molecules_td molecules_tdl">';    
                        echo $datatypefromid[strval($r['datatype'])].'</td><td class="molecules_td ">';
                        echo $r['value'].' '.$r['units'].' </td>';
                        if($r['datacomment']){
                            echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\'';
                            echo $r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\''.str_replace("\r\n","<br />",htmlentities($r['datacomment'])).'\');">';
                            echo '<img src="info_icon.png" height=15 title="Notes Available" />';
                        }else{
                            echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\'';
                            echo $r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\'No Notes.\');">';
                        }
                        echo '</td></tr>';
                }
                if($count==0){
                    echo '<tr><td></td><td><br /><br />No data.</td></tr>';
                }
            ?>    
        </table>
    </div>
    <div id="div_docdata" class="div_data" style="display:none">
        <table id="doctable" class="viewmolecule_datatable">
            <tr><th class="molecules_th">Data Type</th><th class="molecules_th">Link</th><th class="molecules_th">Notes</th></tr>
            <?php
                $count=0;
                foreach($response as $r){
                    if(!in_array(strval($r['datatype']),$docdataids))continue;
                    $count++;
                    echo '<tr><td class="molecules_td molecules_tdl">';    
                        echo $datatypefromid[strval($r['datatype'])].'</td><td class="molecules_td ">';
                        $filename=exec('ls uploads/documents/'.$thismolid.'_'.$r['datatype'].'_'.$r['moldataid'].'*');
                                        echo '<a href="'.$filename.'">View</a>';
                        echo '</td>';
                        if($r['datacomment']){
                            echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\'';
                            echo $r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\''.str_replace("\r\n","<br />",htmlentities($r['datacomment'])).'\');">';
                            echo htmlentities(substr($r['datacomment'],0,20));
                            if(strlen($r['datacomment'])>20) echo '...<a href="#">(more)</a>';
                        }else{
                            echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\'';
                            echo $r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\'No Notes.\');">';
                        }
                        echo '</td></tr>';
                }
                if($count==0){
                    echo '<tr><td></td><td><br /><br />No data.</td></tr>';
                }
            ?>    
        </table>
    </div>
    <div id="div_modelingdata" class="div_data" style="display:none">
        <table id="modelingtable" class="viewmolecule_datatable">
        <tr><th class="molecules_th">Data Type</th><th class="molecules_th">Link</th></tr>
        <?php    
            if(file_exists('uploads/sketches/'.$thismolid.'.png')){
                echo '<tr><td class="molecules_td molecules_tdl">PNG Image</td>';
                echo '<td class="molecules_td molecules_tdr"><a href="uploads/sketches/'.$thismolid.'.png">Download</a></td>';
            }
            if(file_exists('uploads/sketches/'.$thismolid.'.jpg')){
                echo '<tr><td class="molecules_td molecules_tdl">JPG Image</td>';
                echo '<td class="molecules_td molecules_tdr"><a href="uploads/sketches/'.$thismolid.'.jpg">Download</a></td>';
            }        
            if(file_exists('uploads/structures/'.$thismolid.'.mol')){
                echo '<tr><td class="molecules_td molecules_tdl">2D MOL</td>';
                echo '<td class="molecules_td molecules_tdr"><a href="uploads/structures/'.$thismolid.'.mol">Download</a></td>';
            }
            if(file_exists('uploads/structures/'.$thismolid.'-3d.mol')){
                echo '<tr><td class="molecules_td molecules_tdl">3D MOL</td>';
                echo '<td class="molecules_td molecules_tdr"><a href="uploads/structures/'.$thismolid.'-3d.mol">Download</a></td>';
            }
            if(file_exists('uploads/qikprop/'.$thismolid.'-QP.txt')){
                echo '<tr><td class="molecules_td molecules_tdl">QikProp Output</td>';
                echo '<td class="molecules_td molecules_tdr"><a href="uploads/qikprop/'.$thismolid.'-QP.txt">Download</a></td>';
            }
        ?>
        </table>
    </div>

    <div id="div_commentdata" class="div_data" style="display:none">
        <div id="div_commentblockspacer" class="div_molcommentblock" style="min-height:5px;"></div>
<?php
        $q = $dbconn->prepare("select molcommentid,molcomment,dateadded,username from molcomments left join users on users.userid=molcomments.authorid where molcomments.molid=:num order by dateadded");
        $q->bindParam(":num",$thismolid,PDO::PARAM_INT);
        $q->execute();
        $count=0;
        while($row=$q->fetch()){
            $count++;
            echo '<div id="div_molcomment_'.$count.'" class="div_molcommentblock">';
                echo '<div class="div_molcomment_author" id="div_molcomment_author_'.$count.'">';
                    echo $row['username'];
                    echo ':<br/> ('.parsetimestamp($row['dateadded']).') ';
                echo '</div>';
                echo '<div class="div_molcomment_text">'.str_replace("\r\n","<br />",htmlentities($row['molcomment'])).'</div>';
                if($row['username']==$_SESSION['username']){
                    echo '<div class="div_deletecomment"><span class="nonlinks"><a href="../cgi-bin/removemolcomment.py?molid='.$thismolid.'&molcommentid='.$row['molcommentid'].'">X</a></span></div>';
                }
                
            echo '</div>';
        }
        if($count==0){
            echo '<br /><br />No comments.';
        }
        echo '<div id="div_addmolcomment">';
        echo '<form action="../cgi-bin/addmolcomment.py?molid='.$thismolid.'&userid='.$_SESSION['userid'].'" method="post">';
        echo '<textarea name="textarea_addmolcomment" id="textarea_addmolcomment" ></textarea><br />';
        echo '<input type="submit" id="commentbutton" value="Add Comment" />';
        echo '</form>';
        echo '</div>';
?>

    </div>
    


    </div>
</div>
</body>
</html>
