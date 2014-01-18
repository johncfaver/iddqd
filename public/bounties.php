<?php
//  bounties.php
//  List open bounties in database.
//
	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		//echo 'Database connection failed: '. $e->getMessage();
	}
	session_start();
	$loggedin = isset($_SESSION['username']);
	if(!$loggedin) returnhome(0);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Bounties</title>
<link rel="stylesheet" href="reset.css" type="text/css" />
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<link rel="stylesheet" href="bounty.css" type="text/css" />
<script type="text/javascript" src="iddqd.js"></script>
</head>
<body>
<div id="div_holder">
<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
	    <?php if ($_SESSION['isadmin']) echo '<a href="admin.php" style="color:white">Administration</a><br/><br/>';?>
    	<a href="index.php" style="color:white">Home</a><br /><br />
		<a href="search.php" style="color:white">Search </a><br /><br />
		<a href="molecules.php" style="color:white">View Library</a><br /><br />
		<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
        <a href="bounties.php" style="color:#bbbbff">Bounties</a><br /><br />
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
    <div id="div_post_bounty">
        <a href="postbounty.php"><button type="button" style="width:120px;height:25px;">Post Bounty</button></a>
    </div>
  <?php
    $nummol=(isset($_GET['nummol']))?(int)$_GET['nummol']:8;
  	$molstart=(isset($_GET['molstart']))?(int)$_GET['molstart']:0;

    $qstr = 'SELECT b.bountyid, t.nickname as target, u.username as posted_by, b.date_posted, u2.username as pursued_by,';
    $qstr.= ' b.date_pursued, b.claimed, b.molid, b.date_claimed, m.molname';
   
    $qstr.= ' from bounties b join targets t on t.targetid=b.targetid join users u on b.placed_by_id=u.userid ';
    $qstr.= ' left join users u2 on u2.userid=b.pursued_by_id';
    $qstr.= ' left join molecules m on m.molid=b.molid';
    
    $qstr.= ' order by b.date_claimed';
    $qstr.= ' limit :num1 offset :num2';
   
    $q = $dbconn->prepare($qstr);
    $q->bindParam(":num1",$nummol,PDO::PARAM_INT);
    $q->bindParam(":num2",$molstart,PDO::PARAM_INT);
    $q->execute();
    if($q->rowCount() > 0){
        echo '<table class="moleculetable">
                <tr>
                    <th class="molth moltdborderright">BountyID</th>
                    <th class="molth moltdborderright">Structure</th>
                    <th class="molth moltdborderright">Target</th>
                    <th class="molth moltdborderright">Posted By</th>
                    <th class="molth moltdborderright">Pursued By</th>
                    <th class="molth moltdborderright">Claimed</th>
                    <th class="molth ">Name</th>
                </tr>';
        $response = $q->fetchAll(PDO::FETCH_ASSOC);
        $count=1;
        foreach($response as $bounty){
            if($count%2==0){
	            $tdcolor="moltdcolor";
	        }else{
	            $tdcolor="";
	        }
	        echo '<tr>
	                <td class="moltd '.$tdcolor.' moltdborderright">
	                    <a href="bountypage.php?bid='.$bounty['bountyid'].'">
	                        '.$bounty['bountyid'].'
                        </a>
	                </td>
	                <td class="moltd '.$tdcolor.' moltdborderright">
	                    <a href="bountypage.php?bid='.$bounty['bountyid'].'">
	                        <img src="uploads/bounties/'.$bounty['bountyid'].'.jpg" style="height:60px;"/>
                        </a>
	                </td>
	                <td class="moltd '.$tdcolor.' moltdborderright">
	                    '.$bounty['target'].'
	                </td>
	                <td class="moltd '.$tdcolor.' moltdborderright">
	                    '.$bounty['posted_by'].'
	                </td>
	                <td class="moltd '.$tdcolor.' moltdborderright">
	                    '.$bounty['pursued_by'].'
	                </td>
	                <td class="moltd '.$tdcolor.' moltdborderright">
	                    '.parsetimestamp($bounty['date_claimed']).'
	                </td>
	                <td class="moltd '.$tdcolor.' ">';
	                    if($bounty['claimed']){
	                        echo '<a href="viewmolecule.php?molid='.$bounty['molid'].'">'.$bounty['molname'].'</a>';
	                    }
	            echo '</td>
	            </tr>';
        }
        echo '</table>';
    }
?>
</div>
</div>
</body>
</html>
