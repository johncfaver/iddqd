<?php
    
    $debug=0;
    require('../private/cred.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);
    }catch(PDOException $e){
        echo 'Database connection failed: '. $e->getMessage();
    }
    $dbconn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING );
    
    session_start();
    $loggedin = isset($_SESSION['username']);
    if(!$loggedin) returnhome();

	$molstart=(isset($_GET['molstart']))?(int)$_GET['molstart']:0;
	$sortby=(isset($_GET['sortby']))?pg_escape_string($_GET['sortby']):'dateadded';
	$sortdir=(isset($_GET['sortdir']))?(int)$_GET['sortdir']:0;
    $similaritysearch=(isset($_GET['similarity']))?(int)$_GET['similarity']:false;	
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
	/*if($molstart>=$nummol){
		echo '<div id="div_molecules_prev"><span class="nonlinks"><a href="molecules.php?molstart='.($molstart-$nummol).'&sortby='.$sortby.'"> << previous </a></span></div>';
	}
	if($molstart+$nummol<$dbcountmol){
		echo '<div id="div_molecules_next"><span class="nonlinks"><a href="molecules.php?molstart='.($molstart+$nummol).'&sortby='.$sortby.'"> next>></a></span></div>';
	}*/
?>

    <span style="font-size:0.8em;">Found <?php echo $numresults;?> results.</span>

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
	    $q = $dbconn->prepare($qstr);
	    $q->execute();
    }
?>

    <table class="moleculetable">
        <tr class="moltr">
        <th class="molth moltdborderright">Structure</th> 
        <th class="molth moltdborderright">Name</th> 
        <th class="molth moltdborderright">MW</th> 
        <th class="molth moltdborderright">Author</th> 
        <th class="molth <?php if($similaritysearch) echo 'moltdborderright';?>">Date Added</th> 
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
<?php
    if($numresults==0) echo '<br/><br/>No results.';
?>

</div>
</body>
</html>

