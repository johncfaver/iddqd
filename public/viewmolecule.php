<?php

// viewmolecule.php
// display information for a molecule specified by $_GET['molid']
//

    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);    
    }catch(PDOException $e){
        //echo 'Database connection failed: '. $e->getMessage();
    }

    session_start();
    $loggedin = isset($_SESSION['username']) and isset($_SESSION['userid']);
    if(!$loggedin) returnhome(0);
    $thismolid = isset($_GET['molid'])?(int)pg_escape_string($_GET['molid']):-1;
    if($thismolid < 0) returnhome(10);

    $q = $dbconn->prepare("SELECT 
                            m.molname,
                            m.authorid,
                            u.username,
                            m.dateadded,
                            m.molweight,
                            m.molformula,
                            m.iupac,
                            m.cas 
                           FROM molecules m 
                            LEFT JOIN users u ON m.authorid=u.userid
                           WHERE m.molid=:num
                          ");
    $q->bindParam(":num",$thismolid,PDO::PARAM_INT);
    $q->execute();
    if($q->rowCount() != 1) returnhome(11);
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
<div id="div_holder">
<div id="div_datapopup"></div>
<div id="div_shade_window"></div>
<div id="div_deletecheck" class="div_notespopup" >
    <form action="../cgi-bin/deletemolecule.py" method="post">
        <input type="hidden" name="molid" value="<?php echo $thismolid;?>" />
        <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
        <span class="span_popup_main_text">
            Are you sure you want to delete this molecule?
        </span>
        <input type="submit" value="Delete" class="button_popup button_popup_left" />
        <input type="button" value="Cancel" class="button_popup button_popup_right"  onclick="closedeletecheck();return false"/>
    </form>
</div>

<div id="div_left">
    <div id="left_links">
        <span class="nonlinks">
            <?php if ($_SESSION['isadmin']) echo '<a href="admin.php" style="color:white">Administration</a><br/><br/>';?>
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
            var viewerCanvas = new ChemDoodle.ViewerCanvas('viewerCanvas', 500, 300);
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
        <br /><br/>Added by: <?php echo $moldata['username'];?> on <?php echo parsetimestamp($moldata['dateadded']);?>
<?php
            if($moldata['username']==$_SESSION['username']){
                echo '&nbsp;<a href="#" onclick="deletecheck();return false;">(delete)</a>';
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
//We'll grab all data and data comments associated with molid and sort them later when generating tables.        
//Molecule comments are queried separately.
        $q = $dbconn->prepare("SELECT DISTINCT 
                                    u.username,
                                    t.nickname,
                                    c.datacomment,
                                    d.value,
                                    d.dateadded,
                                    d.datatype,
                                    d.moldataid,
                                    dt.type,
                                    dt.units,
                                    dt.class
                                FROM moldata d
                                    LEFT JOIN targets t ON d.targetid=t.targetid 
                                    LEFT JOIN datacomments c ON d.moldataid=c.dataid 
                                    LEFT JOIN users u ON u.userid=d.authorid 
                                    LEFT JOIN datatypes dt ON dt.datatypeid=d.datatype 
                                WHERE molid=:num 
                                ORDER BY dateadded");
        $q->bindParam(":num",$thismolid,PDO::PARAM_INT);
        $q->execute();
        $response=$q->fetchAll();
?>
<!-- SELECTION TABS -->
    <span class="nonlinks">
        <a href="#"><div id="div_tabbindingdata" class="datatab datatabopen" onclick="switchdatadiv('bindingdata');return false">Binding</div></a>
        <a href="#"><div id="div_tabpropertydata" class="datatab" onclick="switchdatadiv('propertydata');return false">Properties</div></a>
        <a href="#"><div id="div_tabdocdata" class="datatab" onclick="switchdatadiv('docdata');return false">Documents</div></a>
        <a href="#"><div id="div_tabmodelingdata" class="datatab" onclick="switchdatadiv('modelingdata');return false">Modeling</div></a>
        <a href="#"><div id="div_tabcommentdata" class="datatab" onclick="switchdatadiv('commentdata');return false">Comments</div></a>
        <a href="editmolecule.php?molid=<?php echo $thismolid;?>"><div id="div_editdata" class="datatab" >Edit</div></a>
<?php
    if(!in_array($thismolid,$_SESSION['notebook_molids'])){
        echo '<a href="addtonotebook.php?molid='.$thismolid.'&dest=vm"><div id="div_addtonotebook" class="datatab" >Add to Notebook</div></a>';
    }else{
        echo '<a href="removefromnotebook.php?molid='.$thismolid.'&dest=vm"><div id="div_addtonotebook" class="datatab" >Remove from Notebook</div></a>';
    }
?>
    </span>

<!-- BINDING DATA TAB -->
    <div id="div_bindingdata" class="div_data">
        <table id="bindingtable" class="viewmolecule_datatable">
            <tr>
                <th class="molecules_th">Data Type</th>
                <th class="molecules_th">Value</th>
                <th class="molecules_th">Target</th>
                <th class="molecules_th">Notes</th>
            </tr>
            <?php
                $count=0;
                foreach($response as $r){
                    if($r['class']!=1) continue;
                    $count++;
                    echo '<tr>
                            <td class="molecules_td molecules_tdl">    
                                '.$r['type'].'
                            </td>
                            <td class="molecules_td molecules_tdr">
                                '.$r['value'].' '.$r['units'].'
                            </td>
                            <td class="molecules_td">
                                '.$r['nickname'].'
                            </td>';
                    if($r['datacomment']){
                        $comment = htmlentities($r['datacomment']);
                        echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\''.$r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\''.str_replace("\r\n",'<br />',addslashes($comment)).'\');return false">
                            <img src="info_icon.png" height=15 title="Notes Available" />';
                    }else{
                        echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\''.$r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\'No Notes.\');return false">';
                    }
                    echo '  </td>
                        </tr>';
                }
                if($count==0){    
                    echo '<tr><td></td><td><br /><br />No data.</td></tr>';
                }
            ?>    
        </table>
    </div>
<!-- PROPERTY DATA TAB -->
    <div id="div_propertydata" class="div_data" style="display:none">
        <table id="propertytable" class="viewmolecule_datatable">
            <tr>
                <th class="molecules_th">Data Type</th>
                <th class="molecules_th">Value</th>
                <th class="molecules_th">Notes</th>
            </tr>
            <?php
                $count=0;
                foreach($response as $r){
                    if($r['class']!=2) continue;
                    $count++;
                    echo '<tr>
                            <td class="molecules_td molecules_tdl">  
                            '.$r['type'].'</td><td class="molecules_td ">
                            '.$r['value'].' '.$r['units'].' </td>';
                        if($r['datacomment']){
                            $comment = htmlentities($r['datacomment']);
                            echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\''.$r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\''.str_replace("\r\n","<br />",addslashes($comment)).'\');return false">
                             <img src="info_icon.png" height=15 title="Notes Available" />';
                        }else{
                            echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\''.$r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\'No Notes.\');return false">';
                        }
                        echo '</td>
                          </tr>';
                }
                if($count==0){
                    echo '<tr><td></td><td><br /><br />No data.</td></tr>';
                }
            ?>    
        </table>
    </div>
<!-- DOCUMENT DATA TAB -->
    <div id="div_docdata" class="div_data" style="display:none">
        <table id="doctable" class="viewmolecule_datatable">
            <tr>
                <th class="molecules_th">Data Type</th>
                <th class="molecules_th">Author</th>
                <th class="molecules_th">File</th>
                <th class="molecules_th">Notes</th>
            </tr>
            <?php
                $count=0;
                foreach($response as $r){
                    if($r['class']!=3)continue;
                    $count++;
                    echo '<tr>
                            <td class="molecules_td molecules_tdl">
                            '.$r['type'].'</td><td class="molecules_td">'.$r['username'].' ('.parsetimestamp($r['dateadded']).')</td><td class="molecules_td ">';
                        
                        //Find filename for this data entry.
                        $tarray = glob('uploads/documents/'.$thismolid.'_'.$r['datatype'].'_'.$r['moldataid'].'*');
                        if (count($tarray)==1){
                            $filename = htmlentities($tarray[0]);
                            $basename = preg_replace('/^(\d+_){3}/','',str_replace('uploads/documents/','',$filename));
                            echo '<a href="'.$filename.'">'.$basename.'</a>';
                        }else{
                            unset($tarray);
                        }
                        echo '</td>';
                        if($r['datacomment']){
                            $comment = htmlentities($r['datacomment']);
                            echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\''.$r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\''.str_replace("\r\n","<br />",addslashes($comment)).'\');return false">
                                '.str_replace("\r\n", " ",htmlentities(substr($comment,0,20)));
                            if(strlen($comment)>20) echo '...<a href="#">(more)</a>';
                        }else{
                            echo '<td class="molecules_td molecules_tdr" onclick="opendatapopup(\''.$r['username'].'\',\''.parsetimestamp($r['dateadded']).'\',\'No Notes.\');return false">';
                        }
                        echo '</td></tr>';
                }
                if($count==0){
                    echo '<tr><td></td><td><br /><br />No data.</td></tr>';
                }
            ?>    
        </table>
    </div>
<!-- MODELING DATA TAB -->
    <div id="div_modelingdata" class="div_data" style="display:none">
        <table id="modelingtable" class="viewmolecule_datatable">
            <tr>
                <th class="molecules_th">Data Type</th>
                <th class="molecules_th">Link</th>
            </tr>
        <?php    
            if(file_exists('uploads/sketches/'.$thismolid.'.png')){
                echo '<tr><td class="molecules_td molecules_tdl">PNG Image</td>
                    <td class="molecules_td molecules_tdr"><a href="uploads/sketches/'.$thismolid.'.png">Download</a></td>';
            }
            if(file_exists('uploads/sketches/'.$thismolid.'.jpg')){
                echo '<tr><td class="molecules_td molecules_tdl">JPG Image</td>
                    <td class="molecules_td molecules_tdr"><a href="uploads/sketches/'.$thismolid.'.jpg">Download</a></td>';
            }        
            if(file_exists('uploads/structures/'.$thismolid.'.mol')){
                echo '<tr><td class="molecules_td molecules_tdl">2D MOL</td>
                    <td class="molecules_td molecules_tdr"><a href="uploads/structures/'.$thismolid.'.mol">Download</a></td>';
            }
            if(file_exists('uploads/structures/'.$thismolid.'-3d.mol')){
                echo '<tr><td class="molecules_td molecules_tdl">3D MOL</td>
                    <td class="molecules_td molecules_tdr"><a href="uploads/structures/'.$thismolid.'-3d.mol">Download</a></td>';
            }
            if(file_exists('uploads/qikprop/'.$thismolid.'-QP.txt')){
                echo '<tr><td class="molecules_td molecules_tdl">QikProp Output</td>
                    <td class="molecules_td molecules_tdr"><a href="uploads/qikprop/'.$thismolid.'-QP.txt">Download</a></td>';
            }
        ?>
        </table>
    </div>
<!-- MOLECULE COMMENT DATA TAB -->
    <div id="div_commentdata" class="div_data" style="display:none">
        <div id="div_commentblockspacer" class="div_molcommentblock" style="min-height:5px;"></div>
<?php
        $q = $dbconn->prepare("SELECT 
                                c.molcommentid,
                                c.molcomment,
                                c.dateadded,
                                u.username 
                              FROM molcomments c 
                                LEFT JOIN users u on u.userid=c.authorid 
                              WHERE 
                                c.molid=:num 
                              ORDER BY dateadded");

        $q->bindParam(":num",$thismolid,PDO::PARAM_INT);
        $q->execute();
        $count=0;
        while($row=$q->fetch()){
            $count++;
            echo '<div id="div_molcomment_'.$count.'" class="div_molcommentblock">
                    <div class="div_molcomment_author" id="div_molcomment_author_'.$count.'">
                    '.$row['username'].'
                    :<br/> ('.parsetimestamp($row['dateadded']).') 
                    </div>
                    <div class="div_molcomment_text">
                        '.str_replace("\r\n","<br />",htmlentities($row['molcomment'])).'
                    </div>';
                if($row['username']==$_SESSION['username']){
                    echo '<div class="div_deletecomment">
                            <form action="../cgi-bin/removemolcomment.py" method="POST">
                                <input type="hidden" name="molid" value="'.$thismolid.'" />
                                <input type="hidden" name="molcommentid" value="'.$row['molcommentid'].'" />
                                <input type="hidden" name="userid" value="'.$_SESSION['userid'].'" />
                                <input type="hidden" name="token" value="'.$_SESSION['token'].'" />
                                <input type="submit" class="button_link" value="X   "/>
                            </form>
                          </div>';
                }
            echo '</div>';
        }
        if($count==0){
            echo '<br /><br />No comments.';
        }
?>
            <div id="div_addmolcomment">
                <form action="../cgi-bin/addmolcomment.py" method="POST">
                    <input type="hidden" name="molid" value="<?php echo $thismolid;?>" />
                    <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
                    <textarea name="textarea_addmolcomment" id="textarea_addmolcomment" ></textarea><br />
                    <input type="submit" id="commentbutton" value="Add Comment" />
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>
