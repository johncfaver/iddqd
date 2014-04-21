<?php
//Page for viewing information about targets.
//Revision of viewmolecule.php for target data.

    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);    
    }catch(PDOException $e){
        //echo 'Database connection failed: '. $e->getMessage();
    }

    session_start();
    $loggedin = isset($_SESSION['username']) and isset($_SESSION['userid']);
    if(!$loggedin) returnhome(0);
    $thistargetid = isset($_GET['targetid'])?(int)pg_escape_string($_GET['targetid']):-1;
    if($thistargetid < 0) returnhome(12);

    //Get general data about the target. 
    $q = $dbconn->prepare("SELECT 
                            t.nickname,
                            t.fullname,
                            t.targetclass,
                            t.series,
                            t.dateadded,
                            u.username
                           FROM targets t LEFT JOIN 
                            users u ON u.userid=t.authorid
                           WHERE targetid=:num
                          ");
    $q->bindParam(":num",$thistargetid,PDO::PARAM_INT);
    $q->execute();
    if($q->rowCount() != 1) returnhome(13);
    $targetdata=$q->fetch(PDO::FETCH_ASSOC);

    //Get data about all known inhibitors for this target. 
    //Order inhibitors by binding affinity. 
    $q = $dbconn->prepare(" SELECT * from 
							   (SELECT DISTINCT ON (d.molid)
							    d.value,
							    d.molid,
							    m.molname,
							    k.type,
							    k.units,
							    c.datacomment,
                                c.dateadded as commentdate,
							    u.username as commenter
							    FROM moldata d 
							        LEFT JOIN molecules m on m.molid=d.molid 
							        LEFT JOIN datatypes k on k.datatypeid=d.datatype
							        LEFT JOIN datacomments c on c.dataid=d.moldataid
							        LEFT JOIN users u on u.userid=c.authorid
							    WHERE d.targetid=:num and d.datatype in (1,2,3)
							    ORDER BY d.molid, d.value
							    ) AS temp 
                            ORDER BY temp.value ");
    $q->bindParam(":num",$thistargetid,PDO::PARAM_INT);
    $q->execute();
    $numtotmol=$q->rowCount();
    $nmolperpage=8;
    $inhibitordata=$q->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="reset.css" type="text/css" />
    <link rel="stylesheet" href="iddqd.css" type="text/css" />
    <link rel="stylesheet" href="viewtarget.css" type="text/css" />
    <script type="text/javascript" src="iddqd.js"></script>
    <script type="text/javascript" src="viewtarget.js"></script>
    <script type="text/javascript">
<?php 
    //Place $inhibitordata in javascript array to avoid repetitive database lookups.
    foreach($inhibitordata as $r){
        echo 'inhibitors.push(new inhibitorEntry("'.$r['molid'].'","'.$r['molname'].'","'.$r['value'].' '.$r['units'].'","'.$r['type'].'","'.str_replace("\r\n","<br/>",addslashes(htmlentities($r['datacomment']))).'","'.$r['commenter'].'","'.parsetimestamp($r['commentdate']).'"));';
    }
?>
    </script>
    <title><?php echo htmlentities($targetdata['nickname'], ENT_QUOTES); ?></title>
</head>

<body>
<div id="div_holder">
<div id="div_datapopup"></div>
<div id="div_shade_window"></div>
<div id="div_deletecheck" class="div_notespopup" >
   <?php
    /* I'm not sure about the best way to handle deleting a target. Should we also delete every compound associated with it? 
        Some compounds might have multiple associated targets.
        What about documents about the target? 
     <form action="" method="post">
        <input type="hidden" name="molid" value="<?php echo $thistargetid;?>" />
        <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />*/
   ?>
        <span class="span_popup_main_text">
           Not implemented. 
        </span>
   <?php /* 
        <input type="submit" value="Delete" class="button_popup button_popup_left" />*/?>
		<input type="button" value="Cancel" class="button_popup button_popup_right"  onclick="closedeletecheck();return false"/>
   <?php //</form>?>
</div>
<div id="div_left">
    <div id="left_links">
        <span class="nonlinks">
	        <?php if ($_SESSION['isadmin']) echo '<a href="admin.php" style="color:white">Administration</a><br/><br/>';?>
            <a href="index.php" style="color:white">Home</a><br /><br />
	        <a href="search.php" style="color:white">Search</a> <br /><br />
	        <a href="molecules.php" style="color:white">View Library</a><br /><br />
	        <a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
	        <a href="bounties.php" style="color:white">Bounties</a><br /><br />
	        <a href="targets.php" style="color:#bbbbff">Targets</a><br /><br />
	        <a href="help.php" style="color:white">Help</a><br /><br />
        </span>
    </div>
    <!--<div id="div_ad">
        <a href="http://web.chemdoodle.com"><img src="chemdoodleweb.png" /></a>
    </div>-->
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
    <div id="div_molentry">
        <span style="font-size:2.0em;">Target: <?php echo htmlentities($targetdata['nickname'], ENT_QUOTES);?></span>
        <br /><br/>Added by: <?php echo $targetdata['username'];?> on <?php echo parsetimestamp($targetdata['dateadded']);?>
<?php
            if($targetdata['username']==$_SESSION['username']){
                echo '&nbsp;<a href="#" onclick="deletecheck();return false;">(delete)</a>';
            }
?>

        <table id="table_molinfo">
            <tr class="molecules_tr">
                <td class="molecules_td molecules_tdl molecules_tdr">Full Name</td>
                <td class="molecules_td molecules_tdl"><?php echo $targetdata['fullname'];?></td>
                <td class="molecules_td molecules_tdl molecules_tdr">Inhibitors:</td>
                <td class="molecules_td molecules_tdr molecules_tdl "><?php echo $numtotmol;?></td>
            </tr>
            <tr class="molecules_tr">
                <td class="molecules_td molecules_tdr molecules_tdl">Series</td>
                <td class="molecules_td molecules_tdl"><?php echo $targetdata['series'];?></td>
                <td class="molecules_td molecules_tdl molecules_tdr">Class</td>
                <td class="molecules_td molecules_tdr molecules_tdl"><?php echo $targetdata['targetclass'];?></td>
            </tr>
        </table>    
    </div>

    <div id="div_moldata">

        <span class="nonlinks">
            <a href="#"><div id="div_tabbindingdata" class="datatab datatabopen" onclick="switchdatadiv('bindingdata');return false;">Inhibitors</div></a>
            <a href="#"><div id="div_tabdocdata" class="datatab" onclick="switchdatadiv('docdata');return false">Documents</div></a>
            <a href="#"><div id="div_tabmodelingdata" class="datatab" onclick="switchdatadiv('modelingdata');return false">Modeling</div></a>
            <a href="edittarget.php?targetid=<?php echo $thistargetid;?>"><div id="div_editdata" class="datatab" >Edit</div></a>
            <a href="addtonotebook.php?targetid=<?php echo $thistargetid;?>&amp;dest=vt"><div id="div_addtonotebook" class="datatab" >Add inhibitors to Notebook</div></a>
        </span>


<!--BINDING DATA -->    
    <div id="div_bindingdata" class="div_data">
    <?php
        if($numtotmol>0){

            if($numtotmol>$nmolperpage){ // if pages are needed
                echo '<div id="div_inhibitorPageLeft"> 
                        <a href="#" onclick="inhibitorPageLeft('.$nmolperpage.');return false;"> &lt;&lt; Previous </a>
                      </div>';
            }

                echo '<table id="bindingtable" class="viewmolecule_datatable">
                        <tr>
                            <th class="molecules_th">
                                Name
                            </th>
                            <th class="molecules_th">
                                Activity 
                            </th>
                            <th class="molecules_th">
                                Data Type
                            </th>
                            <th class="molecules_th">
                                Notes
                            </th>
                        </tr>';

            for($i=0;$i<$nmolperpage;$i++){
                  echo '<tr></tr>';   //one row for each inhibitor on this page. The row is to be filled with javascript:showInhibitors()
            }

            echo '</table>';
           
            if($numtotmol>$nmolperpage){ // if pages are needed
                echo '<div id="div_inhibitorPageRight"> 
                        <a href="#" onclick="inhibitorPageRight('.$nmolperpage.');return false;"> &gt;&gt; Next </a>
                      </div>';
            }

            // Load inhibitor data into table
            echo '<script type="text/javascript">showInhibitors(0);</script>';

        }else{
            echo '<br/><br/><br /><br />No inhibitors found.';
        }
    ?>    
    </div>

<!--Document data -->
    <div id="div_docdata" class="div_data" style="display:none">
<?php
    //Query database for data about target-related documents.
    $q = $dbconn->prepare("SELECT DISTINCT
                                 t.type,
                                 d.datatype,
                                 d.targetdatacomment,
                                 d.authorid,
                                 d.dateadded,
                                 u.username,
                                 d.targetdataid
                                FROM targetdata d 
                                    LEFT JOIN datatypes t ON t.datatypeid=d.datatype
                                    LEFT JOIN users u ON u.userid=d.authorid
                                WHERE d.targetid=:num
                                ORDER BY d.dateadded");
    $q->bindParam("num",$thistargetid,PDO::PARAM_INT);
    $q->execute();
    if($q->rowCount()==0){
        echo '<br/><br/><br/><br/>No documents available.';
    }else{
        echo '<table id="doctable" class="viewmolecule_datatable">
                <tr>
                    <th class="molecules_th">Data Type</th>
                    <th class="molecules_th">Author</th>
                    <th class="molecules_th">File</th>
                    <th class="molecules_th">Notes</th>
                </tr>';

        while($row=$q->fetch(PDO::FETCH_ASSOC)){
            $comment = htmlentities($row['targetdatacomment']);
            $dataid = $row['targetdataid'];
            $datatype = $row['type'];
            $datatypeid = $row['datatype'];
            $author = $row['username'];
            $date = parsetimestamp($row['dateadded']);

            $tarray = glob('uploads/targets/'.$thistargetid.'_'.$dataid.'_'.$datatypeid.'_'.'*');
            $fullfilename=(count($tarray)==1)?$tarray[0]:''; //Full filename for linking
            $basename=str_replace('uploads/targets/','',$fullfilename); //Strip directory to get server's basename for presentation to user
            $basename=preg_replace('/^(\d+_){3}/','',$basename); //Strip prepended ID code to get original basename
            $basename=htmlentities($basename);
            if(strlen($basename)>20){
                $basename=substr($basename,0,17).'...';
            }
            echo '<tr>
                <td class="molecules_td molecules_tdl">'.$datatype.'</td>
                <td class="molecules_td">'.$author.' ('.$date.')</td>
                <td class="molecules_td"><a href="'.htmlentities($fullfilename).'">'.$basename.'</a></td>
                <td class="molecules_td molecules_tdr" '; 
            if($comment){
                echo 'onclick="opendatapopup(\''.$author.'\',\''.$date.'\',\''.str_replace("\r\n","<br/>",addslashes($comment)).'\');return false">';
                if(strlen($comment)>20){
                    echo substr($comment,0,17).'...';
                }else{
                    echo $comment;
                }
            }else{
                echo '>';
            }
            echo '</td></tr>';
        }
    }
?>


        </table>

    </div>

<!--Modeling data-->
    <div id="div_modelingdata" class="div_data" style="display:none">
        <br/><br/><br/><br/>No modeling implemented.
    </div>

    </div>
</div>
</div>
</body>
</html>
