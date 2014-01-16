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
    
    $q = $dbconn->query("SELECT COUNT(molid) FROM molecules");
    $r = $q->fetch();
    $ntotmol=$r['count'];

?>
<!DOCTYPE html>
<html>
<head>
<title>Molecules</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="reset.css" type="text/css" />
<link rel="stylesheet" href="iddqd.css" type="text/css" />
</head>
<body>

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
        <a href="notebook.php">My Notebook: <?php echo count($_SESSION['notebook_molids'])+count($_SESSION['notebook_bountyids']); ?></a>
    </div>
    <div id="div_login">
        <span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?> <a href="logout.php">(logout)</a></span>
    </div>    
</div>
<div id="div_main">
<?php
    $nummol=(isset($_GET['nummol']))?(int)pg_escape_string($_GET['nummol']):8;
    $molstart=(isset($_GET['molstart']))?(int)pg_escape_string($_GET['molstart']):0;
    $sortby=(isset($_GET['sortby']))?pg_escape_string($_GET['sortby']):'dateadded';
    $sortdir=(isset($_GET['sortdir']))?(int)pg_escape_string($_GET['sortdir']):1;
    
    if($molstart>=$nummol){
        echo '<div id="div_molecules_prev" class="nonlinks"><a href="molecules.php?molstart='.($molstart-$nummol).'&sortby='.$sortby.'&sortdir='.$sortdir.'"> << previous </a></div>';
    }
    if($molstart+$nummol<$ntotmol){
        echo '<div id="div_molecules_next" class="nonlinks"><a href="molecules.php?molstart='.($molstart+$nummol).'&sortby='.$sortby.'&sortdir='.$sortdir.'"> next>></a></div>';
    }
?>
 <div id="div_molecules_numresults" class="nonlinks">Found <?php echo $ntotmol;?> results.</div>

<table class="moleculetable" >
    <tr class="moltr">
        <th class="molth moltdborderright">Structure</th> 
        <th class="molth moltdborderright"><a href="molecules.php?sortby=molname&sortdir=<?php echo ($sortdir)?0:1;?>">Name</a></th> 
        <th class="molth moltdborderright"><a href="molecules.php?sortby=molweight&sortdir=<?php echo ($sortdir)?0:1;?>">MW</a></th> 
        <th class="molth moltdborderright"><a href="molecules.php?sortby=username&sortdir=<?php echo ($sortdir)?0:1;?>">Author</a></th> 
        <th class="molth "><a href="molecules.php?sortby=dateadded&sortdir=<?php echo ($sortdir)?0:1;?>">Date Added</a></th> 
    </tr>
<?php
    $qstr = 'SELECT 
                m.molid,
                m.molname,
                m.dateadded,
                u.username,
                m.molweight
              FROM molecules m LEFT JOIN users u ON u.userid=m.authorid ';    

    if($sortby=='dateadded'){
         $qstr.=' order by m.dateadded';
         if($sortdir) $qstr.=' DESC';
    }elseif($sortby=='molweight'){
         $qstr.=' order by m.molweight';
         if($sortdir) $qstr.=' DESC';
    }elseif($sortby=='molname'){
         $qstr.=' order by m.molname';
         if($sortdir) $qstr.=' DESC';
    }elseif($sortby=='username'){
        $qstr.=' order by u.username';
        if($sortdir) $qstr.=' DESC';
    }
    
    $qstr.=' limit :num1 offset :num2';
    $q = $dbconn->prepare($qstr); 
    $q->bindParam(":num1",$nummol,PDO::PARAM_INT);
    $q->bindParam(":num2",$molstart,PDO::PARAM_INT);
    $q->execute();
    $response=$q->fetchAll(PDO::FETCH_ASSOC);
    $count=1;
    foreach($response as $entry){
        $tdcolor=($count%2==0)?'moltdcolor':'';
        echo '<tr class="moltr">
                <td class="moltd '.$tdcolor.' moltdborderright">
                    <a href="viewmolecule.php?molid='.$entry['molid'].'">
                        <img src="uploads/sketches/'.$entry['molid'].'.jpg" style="height:60px"/>
                    </a>
                </td>
                <td class=" moltd '.$tdcolor.' moltdborderright">
                    <a href="viewmolecule.php?molid='.$entry['molid'].'">'.htmlentities($entry['molname']).'</a>
                </td>
                <td class="moltd '.$tdcolor.' moltdborderright">
                    '.$entry['molweight'].'
                </td>
                <td class="moltd '.$tdcolor.' moltdborderright">
                    '.$entry['username'].'
                </td>
                <td class="moltd '.$tdcolor.'">
                    '.parsetimestamp($entry['dateadded']).'
                </td>
            </tr>';    
        $count++;
    }    
?>    
</table>
</div>
</body>
</html>
