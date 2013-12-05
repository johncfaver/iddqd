<?php
    //Revision of viewmolecule.php
    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);    
    }catch(PDOException $e){
        //echo 'Database connection failed: '. $e->getMessage();
    }

    session_start();
    $loggedin = isset($_SESSION['username']) and isset($_SESSION['userid']);
    $thistargetid = isset($_GET['targetid'])?(int)pg_escape_string($_GET['targetid']):-1;

    if(!$loggedin or $thistargetid<0) returnhome();

    $q = $dbconn->prepare("SELECT 
                            t.nickname,
                            t.fullname,
                            t.targetclass,
                            t.series,
                            t.dateadded,
                            u.username
                           FROM targets t left join users u on u.userid=t.authorid
                           WHERE targetid=:num
                          ");
    $q->bindParam(":num",$thistargetid,PDO::PARAM_INT);
    $q->execute();
    if($q->rowCount() != 1) returnhome();
    $targetdata=$q->fetch(PDO::FETCH_ASSOC);

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
							    left join molecules m on m.molid=d.molid 
							    left join datatypes k on k.datatypeid=d.datatype
							    left join datacomments c on c.dataid=d.moldataid
							    left join users u on u.userid=c.authorid
							    where d.targetid=:num and d.datatype in (1,2,3)
							    ORDER BY d.molid, d.value
							    ) AS temp 
                            ORDER BY temp.value ");
    $q->bindParam(":num",$thistargetid,PDO::PARAM_INT);
    $q->execute();
    $numtotmol=$q->rowCount();
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
    foreach($inhibitordata as $r){
        echo 'inhibitors.push(new inhibitorEntry("'.$r['molid'].'","'.$r['molname'].'","'.$r['value'].' '.$r['units'].'","'.$r['type'].'","'.str_replace("\r\n","<br/>",addslashes(htmlentities($r['datacomment']))).'","'.$r['commenter'].'","'.parsetimestamp($r['commentdate']).'"));';
    }
?>
</script>

<title><?php echo htmlentities($targetdata['nickname'], ENT_QUOTES); ?></title>
</head>
<body>
<div id="div_datapopup"></div>
<div id="div_shade_window"></div>
<div id="div_deletecheck" class="div_notespopup" >
   <?php
    /* <form action="" method="post">
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
    <a href="addtonotebook.php?targetid=<?php echo $thistargetid;?>&dest=vt"><div id="div_addtonotebook" class="datatab" > Add inhibitors to Notebook</div></a>
    </span>


<!--BINDING DATA -->    
    <div id="div_bindingdata" class="div_data">
    <?php
        if(count($inhibitordata)>0){
            echo '
                <div id="div_inhibitorPageLeft"> <a href="#" onclick="inhibitorPageLeft();return false;"> &lt;&lt; Previous </a></div>
                <table id="bindingtable" class="viewmolecule_datatable">
                    <tr><th class="molecules_th">Name</th><th class="molecules_th">Value</th><th class="molecules_th">Data Type</th><th class="molecules_th">Notes</th></tr>';
            for($i=0;$i<8;$i++){
                echo '<tr></tr>';
            }
            echo '</table><div id="div_inhibitorPageRight"> <a href="#" onclick="inhibitorPageRight();return false;"> &gt;&gt; Next </a></div>';
            echo '<script type="text/javascript">showInhibitors(0);</script>';
        }else{
            echo '<br /><br />No data.';
        }
    ?>    
    </div>

<!--Document data -->
    <div id="div_docdata" class="div_data" style="display:none">
    </div>

<!--Modeling data-->
    <div id="div_modelingdata" class="div_data" style="display:none">
    </div>

    </div>
</div>
</body>
</html>
