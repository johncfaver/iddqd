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

    $numdisplay = (isset($_GET['numdisplay']))?(int)preg_replace("/[^\d]+/","",$_GET['numdisplay']):8;
	$molstart=(isset($_GET['molstart']))?(int)$_GET['molstart']:0;
	$sortby=(isset($_GET['sortby']))?pg_escape_string($_GET['sortby']):'dateadded';
	$sortdir=(isset($_GET['sortdir']))?(int)$_GET['sortdir']:0;
    $similaritysearch=(isset($_GET['similaritysearch']))?(int)$_GET['similaritysearch']:0;	
    if($similaritysearch and !isset($_GET['sortby'])){
        $sortby='similarity';
        $sortdir=1;
    }
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
<div id="div_holder">
<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
	    <?php if ($_SESSION['isadmin']) echo '<a href="admin.php" style="color:white">Administration</a><br/><br/>';?>
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
        echo '<a href="displaysearch.php?molstart='.($molstart-$numdisplay).'&sortby='.$sortby.'&sortdir='.$sortdir.'&similaritysearch='.$similaritysearch.'&numdisplay='.$numdisplay.'"> << Previous </a></div>';
	}
	if($molstart+$numdisplay<$numresults){
		echo '<div id="div_molecules_next" class="nonlinks">';
        echo '<a href="displaysearch.php?molstart='.($molstart+$numdisplay).'&sortby='.$sortby.'&sortdir='.$sortdir.'&similaritysearch='.$similaritysearch.'&numdisplay='.$numdisplay.'"> Next >></a></div>';
	}
?>

    <div id="div_molecules_numresults" class="nonlinks">
        Found <?php echo $numresults;?> result<?php if($numresults!=1) echo 's';?>. 
        Displaying <input type="text" id="text_change_numdisplay" size=2 maxlength=3 value="<?php echo $numdisplay;?>" style="width:30px;" onchange="var t=parseInt(document.getElementById('text_change_numdisplay').value,10);window.location.href='displaysearch.php?molstart=<?php echo $molstart;?>&sortby=<?php echo $sortby;?>&sortdir=<?php echo $sortdir;?>&similaritysearch=<?php echo $similaritysearch;?>&numdisplay='+t;" /> per page.
    </div>

<?php
    if($numresults>0){
	    if(!$similaritysearch){
            $qstr = 'SELECT DISTINCT 
                                m.molid,m.molname,m.dateadded,u.username,m.molweight 
                            FROM molecules m 
                                LEFT JOIN users u on u.userid=m.authorid
                                INNER JOIN (SELECT 0 as molid ';
	        foreach($_SESSION['search_results'] as $mid){
	            $qstr.=' UNION SELECT '.$mid.' as molid ';
	        }
            $qstr.=') as t on m.molid=t.molid';
	    }else{
            $qstr = 'SELECT DISTINCT 
                                m.molid,m.molname,m.dateadded,u.username,m.molweight,t.similarity
                            FROM molecules m 
                                LEFT JOIN users u on u.userid=m.authorid
                                INNER JOIN (SELECT 0 as molid, 0 as similarity ';
	        foreach($_SESSION['search_results'] as $mid => $sim){
	            $qstr.=' UNION SELECT '.$mid.' as molid, '.$sim.' as similarity ';
	        }
            $qstr.=') as t on m.molid=t.molid';
	    }
    
        if($sortby=='dateadded') $qstr.=' order by m.dateadded';
        if($sortby=='molweight') $qstr.=' order by m.molweight';
        if($sortby=='molname')   $qstr.=' order by m.molname';
        if($sortby=='username')  $qstr.=' order by u.username';
        if($similaritysearch and $sortby=='similarity') $qstr.=' order by t.similarity';
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
            <th class="molth moltdborderright">
                Structure
            </th> 
            <th class="molth moltdborderright">
                <a href="displaysearch.php?sortby=molname&sortdir=<?php echo ($sortdir)?0:1;?>&similaritysearch=<?php echo $similaritysearch;?>&numdisplay=<?php echo $numdisplay;?>">Name</a>
            </th> 
            <th class="molth moltdborderright">
                <a href="displaysearch.php?sortby=molweight&sortdir=<?php echo ($sortdir)?0:1;?>&similaritysearch=<?php echo $similaritysearch;?>&numdisplay=<?php echo $numdisplay;?>">MW</a>
            </th> 
            <th class="molth moltdborderright">
                <a href="displaysearch.php?sortby=username&sortdir=<?php echo ($sortdir)?0:1;?>&similaritysearch=<?php echo $similaritysearch;?>&numdisplay=<?php echo $numdisplay;?>">Author</a>
            </th> 
            <th class="molth <?php if($similaritysearch) echo 'moltdborderright';?>">
                <a href="displaysearch.php?sortby=dateadded&sortdir=<?php echo ($sortdir)?0:1;?>&similaritysearch=<?php echo $similaritysearch;?>&numdisplay=<?php echo $numdisplay;?>">Date Added</a>
            </th> 

<?php 
        if($similaritysearch){
                echo '<th class="molth">
                        <a href="displaysearch.php?sortby=similarity&sortdir=';
                echo ($sortdir)?0:1;
                echo '&similaritysearch=1&numdisplay='.$numdisplay.'">Similarity</a></th>';
        }
?>

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
	        if($similaritysearch) echo '<td class="moltd '.$tdcolor.'">'.$r['similarity'].'</td>';
	        echo '</tr>';
	        $icount++;
	    }
    }
?>  
    </table>

</div>
</div>
</body>
</html>

