<?php
    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);    
    }catch(PDOException $e){
      //  echo 'Database connection failed: '. $e->getMessage();
    }
    session_start();
    $loggedin = isset($_SESSION['username']);
    if(!$loggedin) returnhome(0);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Targets</title>
    <link rel="stylesheet" href="reset.css" type="text/css" />
    <link rel="stylesheet" href="iddqd.css" type="text/css" />
    <script type="text/javascript" src="iddqd.js"></script>
</head>
<body>
<div id="div_holder">
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
    <div id="div_add_target"><button type="button" id="button_addtarget" onclick="window.location='addtarget.php'">Add Target</button></div>
<table class="moleculetable" >
    <tr class="moltr">
        <th class="molth moltdborderright">Name</th>    
        <th class="molth moltdborderright">Nickname</th>
        <th class="molth moltdborderright">Target Class</th>    
        <th class="molth moltdborderright">Series Name</th>    
        <th class="molth">Inhibitors</th>
    </tr>
<?php
        $q=$dbconn->query("SELECT 
                            t.targetid,
                            t.fullname,
                            t.nickname,
                            t.targetclass,
                            t.series,
                            count(distinct m.molid) 
                           FROM 
                            targets t LEFT JOIN 
                            moldata m ON t.targetid=m.targetid 
                           GROUP BY 
                            t.targetid 
                           ORDER BY count DESC;");
        $icount=0;
        foreach($q as $row){
            $color=($icount%2==0)?'moltdcolor':'';
            echo '<tr>
                    <td class="nonlinks moltd '.$color.' moltdpadding moltdborderright">
                        <a href="viewtarget.php?targetid='.$row['targetid'].'">'.$row['fullname'].'</a>
                    </td>
                    <td class="nonlinks moltd '.$color.' moltdpadding moltdborderright">
                        <a href="viewtarget.php?targetid='.$row['targetid'].'">'.$row['nickname'].'</a>  
                    </td>
                    <td class="moltd '.$color.' moltdpadding moltdborderright">
                        '.$row['targetclass'].'
                    </td>
                    <td class="moltd '.$color.' moltdpadding moltdborderright">
                        '.$row['series'].'
                    </td>
                    <td class="moltd '.$color.' moltdpadding">
                        '.$row['count'].'
                    </td>
                </tr>';
            $icount+=1;
        }    
?>    
    </table>
    
</table>
</div>
</div>
</body>
</html>
