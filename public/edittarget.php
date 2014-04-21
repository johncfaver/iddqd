<?php
/*
    Page for editing data concerning a target.
    Only the target's author can edit the target data.
    Anyone can upload documents for a target.
    Only the document owners can delete or edit comments of their uploaded target documents.
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
    $targetid = isset($_GET['targetid'])?(int)pg_escape_string($_GET['targetid']):-1;
    if($targetid < 0) returnhome(6);
    $q = $dbconn->prepare("SELECT nickname,fullname,targetclass,series FROM targets WHERE targetid = :num");
    $q->bindParam(":num",$targetid,PDO::PARAM_INT);
    $q->execute();
    if($q->rowCount()!=1) returnhome(7);
    $targetdata = $q->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Edit target</title>
    <link rel="stylesheet" href="reset.css" type="text/css" />
    <link rel="stylesheet" href="iddqd.css" type="text/css" />
    <link rel="stylesheet" href="edittarget.css" type="text/css" />
    <script type="text/javascript" src="iddqd.js"></script>
    <script type="text/javascript">
        var num_docdata = 0;
    </script>
</head>
<body onload="">
<div id="div_holder">
<div id="div_left">
    <div id="left_links">
        <span class="nonlinks">
        <?php if ($_SESSION['isadmin']) echo '<a href="admin.php" style="color:white">Administration</a><br/><br/>';?>
        <a href="index.php" style="color:white">Home</a><br /><br />
        <a href="search.php" style="color:white">Search </a><br /><br />
        <a href="molecules.php" style="color:white">View Library</a><br /><br />
        <a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
        <a href="bounties.php" style="color:white">Bounties</a><br /><br />
        <a href="targets.php" style="color:#bbbbff">Targets</a><br /><br />
        <a href="help.php" style="color:white">Help</a><br /><br />
    </span>
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
    <br/><br/>
    Editing target <?php echo $targetdata['nickname'];?>:
    <br /><br/>
    <span style="color:red">
    <?php 
        if(isset($_GET['status'])){
            if($_GET['status']=='nonickname'){
                echo "Nickname is required.";
            }
            elseif($_GET['status']=='notauthor'){
                echo "You don't have permission to edit this target.";
            }
        }
    ?>
    </span>
     
    <form enctype="multipart/form-data" action="cgi-bin/edittarget.py" method="post">

    <div id="div_targetinfo">
         <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />
         <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
         <input type="hidden" name="targetid" value="<?php echo $targetid;?>" />
         *Nickname: <input type="text" name="nickname" maxlength="25" required value="<?php echo $targetdata['nickname'];?>" /> <br/><br/>
         Full Name: <input type="text" name="fullname" maxlength="100" value="<?php echo $targetdata['fullname'];?>" /><br/><br/>
         Class: <input type="text" name="class"  maxlength="25" value="<?php echo $targetdata['targetclass'];?>" /><br/><br/>
         Series: <input type="text" name="series" maxlength="4" value="<?php echo $targetdata['series'];?>" /><br /><br/><br/>
    </div>

    <div id="div_targetdata_upload">
        <div id="upload_header">My Uploaded Data</div>
        <div id="docdatainputlines" class="div_edittarget_inputlines"></div>
    </div>
    <div id="button_moredocdata">
        <a href="#"><img src="add_icon.png" class="nonlinks" onclick="moredocdata();return false" /></a>
    </div>
    <div id="button_lessdocdata">
        <a href="#"><img src="minus_icon.png" class="nonlinks" onclick="lessdocdata();return false" /></a>
    </div>

    <div id="div_edittarget_buttons"> 
        <input type="submit" value="Submit" style="float:left;"/>
        <input type="button" value="Cancel" style="float:right;" onclick="window.location='viewtarget.php?targetid=<?php echo $targetid;?>';"/>
    </div>
  
        <input type="hidden" name="oldtargetdataids" id="input_olddocdataids" value=""/> 
        <input type="hidden" name="oldtargetcommentids" id="input_oldcommentids" value=""/> 
    </form>
</div>
<!--Populate Data -->
<script type="text/javascript">
    var num_docdata = 0;
<?php
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
    $q->bindParam("num",$targetid,PDO::PARAM_INT);
    $q->execute();
    while($row=$q->fetch(PDO::FETCH_ASSOC)){
        if($row['authorid']!=$_SESSION['userid']){ //Only show this user's documents for editing. 
            continue;
        }
        $comment = htmlentities($row['targetdatacomment']);
        $dataid = $row['targetdataid'];
        $datatype = $row['datatype'];

        $tarray = glob('uploads/targets/'.$targetid.'_'.$dataid.'_'.$datatype.'_'.'*');
        $filename=(count($tarray)==1)?$tarray[0]:'';
       
        if($comment){
            echo 'populatedocdata(\''.addslashes($filename).'\','.$dataid.','.$datatype.','.$dataid.',\''.str_replace("\r\n","\\n",addslashes($comment)).'\');';
        }else{
            echo 'populatedocdata(\''.addslashes($filename).'\','.$dataid.','.$datatype.',0,\'\');';
        }
    }
    if($q->rowCount()==0){
        echo 'moredocdata();';
    }
?>

</script>
<div id="div_shade_window">
</div>
<div id="div_deletecheck" class="div_notespopup">
    <form action="../cgi-bin/deletetargetdata.py" method="post">
        <input type="hidden" name="targetid" value="<?php echo $targetid; ?>" />
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

</div>
</body>
</html>
