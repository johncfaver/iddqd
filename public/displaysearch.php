<?php

//displaysearch.php
//Display search results which are held in $_SESSION['search_results']
    
    $debug=0;
    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);
    }catch(PDOException $e){
       // echo 'Database connection failed: '. $e->getMessage();
    }
    $dbconn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING );
    
    session_start();
    $loggedin = isset($_SESSION['username']);
    if(!$loggedin) returnhome(0);

    $numdisplay = (isset($_GET['numdisplay']))?(int)$_GET['numdisplay']:8;
	$molstart=(isset($_GET['molstart']))?(int)$_GET['molstart']:0;
	$sortby=(isset($_GET['sortby']))?pg_escape_string($_GET['sortby']):'dateadded';
	$sortdir=(isset($_GET['sortdir']))?(int)$_GET['sortdir']:0;
  
    $similaritysearch=(isset($_GET['similaritysearch']))?(int)$_GET['similaritysearch']:0;	
    $numresults = (isset($_SESSION['search_results']))?count($_SESSION['search_results']):0;

?>
<!DOCTYPE html>
<html>
<head>
<title>Search Result</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="reset.css" type="text/css" />
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<script type="text/javascript" src="iddqd.js"></script>
</head>
<body>

<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
		<a href="index.php" style="color:white">Home</a><br /><br />
		<a href="search.php" style="color:#bbbbff">Search</a> <br /><br />
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
<div id="div_top">
	<div id="div_notebook">
		<a href="notebook.php">My Notebook: <?php echo count($_SESSION['notebook_molids']); ?></a>
	</div>
	<div id="div_login">
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?><a href="logout.php">(logout)</a></span>
	</div>	
</div>
<div id="div_main">
<?php
	if($molstart>=$numdisplay){
		echo '<div id="div_molecules_prev" class="nonlinks">';
        echo '<a href="displaysearch.php?molstart='.($molstart-$numdisplay).'&sortby='.$sortby.'&sortdir='.$sortdir.'&similaritysearch='.$similaritysearch.'"> << previous </a></div>';
	}
	if($molstart+$numdisplay<$numresults){
		echo '<div id="div_molecules_next" class="nonlinks">';
        echo '<a href="displaysearch.php?molstart='.($molstart+$numdisplay).'&sortby='.$sortby.'&sortdir='.$sortdir.'&similaritysearch='.$similaritysearch.'"> next>></a></div>';
	}
?>

    <div id="div_molecules_numresults" class="nonlinks">Found <?php echo $numresults;?> result<?php if($numresults!=1) echo 's';?>.</div>

<?php
    if($numresults>0){
	    $qstr = 'SELECT DISTINCT m.molid,m.molname,m.dateadded,u.username,m.molweight from molecules m left join users u on u.userid=m.authorid where m.molid in (';
	    if(!$similaritysearch){
	        foreach($_SESSION['search_results'] as $mid){
	            $qstr.=$mid.',';
	        }
	    }else{
	        foreach($_SESSION['search_results'] as $mid => $sim){
	            $qstr.=$mid.',';
	        }
	    }
	    $qstr = rtrim($qstr,',').')';

        if($sortby=='dateadded') $qstr.=' order by m.dateadded';
        if($sortby=='molweight') $qstr.=' order by m.molweight';
        if($sortby=='molname')   $qstr.=' order by m.molname';
        if($sortby=='username')  $qstr.=' order by u.username';
        if($sortdir) $qstr.=' DESC';

        $qstr.= ' limit :num1 offset :num2';

	    $q = $dbconn->prepare($qstr);
        $q->bindParam(":num1",$numdisplay,PDO::PARAM_INT);
        $q->bindParam(":num2",$molstart,PDO::PARAM_INT);
	    $q->execute();
    }
?>

    <table class="moleculetable">
        <tr class="moltr">
        <th class="molth moltdborderright">Structure</th> 
        <th class="molth moltdborderright"><a href="displaysearch.php?sortby=molname&sortdir=<?php echo ($sortdir)?0:1;?>&similaritysearch=<?php echo $similaritysearch;?>">Name</a></th> 
        <th class="molth moltdborderright"><a href="displaysearch.php?sortby=molweight&sortdir=<?php echo ($sortdir)?0:1;?>&similaritysearch=<?php echo $similaritysearch;?>">MW</a></th> 
        <th class="molth moltdborderright"><a href="displaysearch.php?sortby=username&sortdir=<?php echo ($sortdir)?0:1;?>&similaritysearch=<?php echo $similaritysearch;?>">Author</a></th> 
        <th class="molth <?php if($similaritysearch) echo 'moltdborderright';?>">
            <a href="displaysearch.php?sortby=dateadded&sortdir=<?php echo ($sortdir)?0:1;?>&similaritysearch=<?php echo $similaritysearch;?>">Date Added</a></th> 
        <?php if($similaritysearch) echo '<th class="molth">Similarity</th>';?>

        </tr>
<?php
    if($numresults>0){
	    $icount=0;
	    while($r = $q->fetch(PDO::FETCH_ASSOC)){
	        $tdcolor=($icount%2==1)?"":"moltdcolor";
	        echo '<tr class="moltr">';
	        echo '<td class="moltd '.$tdcolor.' moltdborderright"><a href="viewmolecule.php?molid='.$r['molid'].'">';
	            echo '<img src="uploads/sketches/'.$r['molid'].'.jpg" style="height:60px;"/></a></td>';
	        echo '<td class="moltd '.$tdcolor.' moltdborderright"><a href="viewmolecule.php?molid='.$r['molid'].'">'.htmlentities($r['molname']).'</a>';
	        echo '<td class="moltd '.$tdcolor.' moltdborderright">'.$r['molweight'].'</a>';
	        echo '<td class="moltd '.$tdcolor.' moltdborderright">'.htmlentities($r['username']).'</a>';
	        echo '<td class="moltd '.$tdcolor;
	            if($similaritysearch) echo ' moltdborderright';
	            echo '">'.parsetimestamp($r['dateadded']).'</a>';
	        if($similaritysearch) echo '<td class="moltd '.$tdcolor.'">'.$_SESSION['search_results'][$r['molid']].'</td>';
	        echo '</tr>';
	        $icount++;
	    }
    }
?>  
    </table>

</div>
</body>
</html>

