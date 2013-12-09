<?php
	require('config.php');
	try{
		$dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);	
	}catch(PDOException $e){
		//echo 'Database connection failed: '. $e->getMessage();
	}
	session_start();
	$loggedin = isset($_SESSION['username']);
    $bid = isset($_GET['bid'])?(int)pg_escape_string($_GET['bid']):-1;
	if(!$loggedin or $bid<0) returnhome();
    
    $qstr = 'SELECT 
                t.nickname as target,
                u.username as posted_by,
                b.date_posted,
                u2.username as pursued_by,
                b.date_pursued,
                b.claimed,
                b.molid,
                b.date_claimed,
                m.molid,
                m.molname
            FROM bounties b 
                JOIN targets t ON t.targetid=b.targetid 
                JOIN users u ON b.placed_by_id=u.userid 
                LEFT JOIN users u2 ON u2.userid=b.pursued_by_id
                LEFT JOIN molecules m ON b.molid=m.molid
            WHERE b.bountyid=:num1';

    $q = $dbconn->prepare($qstr);
    $q->bindParam(":num1",$bid,PDO::PARAM_INT); 
    $q->execute();
    $bdata = $q->fetch(); 
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Bounty #<?php echo $bid;?></title>
<link rel="stylesheet" href="reset.css" type="text/css" />
<link rel="stylesheet" href="ChemDoodleWeb/install/ChemDoodleWeb.css" type="text/css">
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<link rel="stylesheet" href="bounty.css" type="text/css" />
<link rel="stylesheet" href="viewmolecule.css" type="text/css" />
<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb-libs.js"></script>
<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb.js"></script>
<script type="text/javascript" src="iddqd.js"></script>
<script type="text/javascript" src="viewmolecule.js"></script>
</head>
<body>

<div id="div_shade_window"></div>
<div id="div_deletecheck" class="div_notespopup" >
    <form action="../cgi-bin/deletebounty.py" method="post">
        <input type="hidden" name="bid" value="<?php echo $bid;?>" />
        <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />
        <span class="span_popup_main_text">
            Are you sure you want to delete this Bounty?
        </span>
        <input type="submit" value="Delete" class="button_popup button_popup_left" />
		<input type="button" value="Cancel" class="button_popup button_popup_right"  onclick="closedeletecheck();return false"/>
    </form>
</div>



<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
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
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?><a href="logout.php">(logout)</a></span>
	</div>	
</div>

<div id="div_main">

    <div id="div_bountysketch">
        <script type="text/javascript">
	        var viewerCanvas = new ChemDoodle.ViewerCanvas('viewerCanvas', 400, 200);
	        viewerCanvas.specs.bonds_width_2D = 1.0;
		    viewerCanvas.specs.bonds_saturationWidth_2D = .18;
		    viewerCanvas.specs.bonds_hashSpacing_2D = 2.5;
		    viewerCanvas.specs.atoms_font_size_2D = 13;
		    viewerCanvas.specs.atoms_font_families_2D = ["Helvetica", "Arial", "sans-serif"];
		    viewerCanvas.specs.atoms_displayTerminalCarbonLabels_2D = true;
		    var molfile='<?php
					$fileContents=file_get_contents('uploads/bounties/'.$bid.'.mol');
       				if($fileContents){
        			    echo str_replace(array("\r\n", "\n", "\r", "'"), array("\\n", "\\n", "\\n", "\\'"), $fileContents);
        			}?>';
			var thismol = ChemDoodle.readMOL(molfile);
			thismol.scaleToAverageBondLength(25.0);
			viewerCanvas.loadMolecule(thismol);
			var t = document.getElementById('viewerCanvas');
			t.setAttribute('style','border:1px');
	    </script>
    </div>

	<div id="div_molentry" style="left:400px;"><br/>
		<span style="font-size:1.8em;">Bounty #<?php echo $bid;?></span>
	<?php
			if($bdata['posted_by']==$_SESSION['username']){
	            echo '<br />Posted by you on '; 
                echo parsetimestamp($bdata['date_posted']);
                echo '&nbsp;<a href="#" onclick="deletecheck();return false">(delete)</a>';
            }
?>
		<table id="table_molinfo">
			<tr class="molecules_tr">
				<td class="molecules_td molecules_tdl molecules_tdr">Target</td>
				<td class="molecules_td molecules_tdr molecules_tdl"><?php echo $bdata['target'];?></td>
                <td class="molecules_td molecules_tdr">
                    <?php 
                        if($bdata['claimed']){
                            echo 'Became:';
                        }
                    ?>
                </td>
				<td class="molecules_td molecules_tdr molecules_tdl">
                    <?php 
                        if($bdata['claimed']){
                            echo '<a href="viewmolecule.php?molid='.$bdata['molid'].'">'.$bdata['molname'].'</a>';
                        }
                    ?>
                </td>
			</tr>
            <tr class="molecules_tr">
				<td class="molecules_td molecules_tdl molecules_tdr">Posted by:</td>
				<td class="molecules_td molecules_tdr molecules_tdl"><?php echo $bdata['posted_by'];?></td>
    			<td class="molecules_td molecules_tdr">Post date:</td>
				<td class="molecules_td molecules_tdr molecules_tdl"><?php echo parsetimestamp($bdata['date_posted']);?></td>
			</tr>
			<tr class="molecules_tr">
                <?php 
                    if($bdata['pursued_by']){
				        echo '<td class="molecules_td molecules_tdl molecules_tdr">Pursued by:</td>';
				        echo '<td class="molecules_td molecules_tdr molecules_tdl">'.$bdata['pursued_by'].'</td>';
    			        echo '<td class="molecules_td molecules_tdr">Start date:</td>';
				        echo '<td class="molecules_td molecules_tdr molecules_tdl">'.parsetimestamp($bdata['date_pursued']).'</td>';
                    }else{
                        echo '<td class="molecules_td molecules_tdl molecules_tdr">Pursued by:</td>';
				        echo '<td class="molecules_td molecules_tdr molecules_tdl">No one</td>';
    			        echo '<td class="molecules_td molecules_tdr">';
                        echo '<a href="../cgi-bin/pursuebounty.py?userid='.$_SESSION['userid'].'&bid='.$bid.'">(pursue)</a></td>';
				        echo '<td class="molecules_td molecules_tdr molecules_tdl"></td>';
                    }
               ?>
			</tr>
		    <tr class="molecules_tr">
				<td class="molecules_td molecules_tdl molecules_tdr">Claimed:</td>
                <?php 
                    if($bdata['claimed']){
                        echo '<td class="molecules_td molecules_tdr molecules_tdl">Yes</td>';
    			        echo '<td class="molecules_td molecules_tdr">Completion date:</td>';
				        echo '<td class="molecules_td molecules_tdr molecules_tdl">'.parsetimestamp($bdata['date_claimed']).'</td>';
                    }else{
                        echo '<td class="molecules_td molecules_tdr molecules_tdl">No</td>';
                        if($bdata['pursued_by']==$_SESSION['username']){
                            echo '<td class="molecules_td molecules_tdr">';
                            echo '<a href="../cgi-bin/claimbounty.py?userid='.$_SESSION['userid'].'&bid='.$bid.'">(claim)</a></td>';
				            echo '<td class="molecules_td molecules_tdr molecules_tdl"></td>';
                        }else{
                            echo '<td class="molecules_td molecules_tdr"></td>';
				            echo '<td class="molecules_td molecules_tdr molecules_tdl"></td>';
                        }
                    }
                ?>
			</tr>
		</table>	
	</div>




    <div id="commentholder" style="width:95%;position:absolute;top:220px;left:25px;margin:auto;text-align:center;border-top:1px solid gray;font-size:0.8em;">
<?php
		$q = $dbconn->prepare("select bountycommentid,bountycomment,dateadded,username from bountycomments left join users on users.userid=bountycomments.authorid where bountycomments.bountyid=:num order by dateadded");
		$q->bindParam(":num",$bid,PDO::PARAM_INT);
		$q->execute();
		$count=0;
		while($row=$q->fetch()){
			$count++;
			echo '<div id="div_molcomment_'.$count.'" class="div_molcommentblock">';
				echo '<div class="div_molcomment_author" id="div_molcomment_author_'.$count.'">';
					echo $row['username'];
					echo ':<br/> ('.parsetimestamp($row['dateadded']).') ';
				echo '</div>';
				echo '<div class="div_molcomment_text">'.str_replace("\r\n","<br />",addslashes(htmlentities($row['bountycomment']))).'</div>';
				if($row['username']==$_SESSION['username']){
					echo '<div class="div_deletecomment" style="font-size:1.2em;"><span class="nonlinks"><a href="../cgi-bin/removebountycomment.py?bid='.$bid.'&userid='.$_SESSION['userid'].'&bountycommentid='.$row['bountycommentid'].'">X</a></span></div>';
				}
				
			echo '</div>';
		}
		if($count==0){
			echo '<br /><br />No comments.';
		}
		echo '<div id="div_addmolcomment" style="width:200px;position:absolute;border:0px solid red;left:200px;">';
		echo '<form action="../cgi-bin/addbountycomment.py?bid='.$bid.'&username='.$_SESSION['username'].'&userid='.$_SESSION['userid'].'" method="post">';
		echo '<textarea name="textarea_addbountycomment" id="textarea_addmolcomment"></textarea><br />';
		echo '<input type="submit" id="commentbutton" value="Add Comment" style="top:50px;left:225px;"/>';
		echo '</form>';
		echo '</div>';
?>
 
    </div>

</div>
</body>
</html>
