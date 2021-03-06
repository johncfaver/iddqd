<?php
//  bountypage.php
//  Display information about a specific bounty.
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
    $bid = isset($_GET['bid'])?(int)pg_escape_string($_GET['bid']):-1;
    if($bid<0) returnhome(3);
    
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
<div id="div_holder">

<div id="div_shade_window"></div>
<div id="div_deletecheck" class="div_notespopup" >
    <form action="../cgi-bin/deletebounty.py" method="post">
        <input type="hidden" name="bid" value="<?php echo $bid;?>" />
        <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
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
        <span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?><a href="logout.php">(logout)</a></span>
    </div>    
</div>

<div id="div_main">

    <div id="div_bountysketch">
        <script type="text/javascript">
            var viewerCanvas = new ChemDoodle.ViewerCanvas('viewerCanvas', 500, 300);
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

    <div id="div_bounty_info"><br/>
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
                        echo '<td class="molecules_td molecules_tdl molecules_tdr">
                                Pursued by:
                              </td>
                              <td class="molecules_td molecules_tdr molecules_tdl">
                                '.$bdata['pursued_by'].'
                              </td>
                              <td class="molecules_td molecules_tdr">
                                Start date:
                              </td>
                              <td class="molecules_td molecules_tdr molecules_tdl">
                                '.parsetimestamp($bdata['date_pursued']).'
                              </td>';
                    }else{
                        echo '<td class="molecules_td molecules_tdl molecules_tdr">
                                Pursued by:
                              </td>
                              <td class="molecules_td molecules_tdr molecules_tdl">
                                No one
                              </td>
                              <td class="molecules_td molecules_tdr">
                                <form action="../cgi-bin/pursuebounty.py" method="POST" >
                                    <input type="hidden" name="userid" value="'.$_SESSION['userid'].'" />
                                    <input type="hidden" name="bid" value="'.$bid.'" />
                                    <input type="hidden" name="token" value="'.$_SESSION['token'].'" />
                                    <input type="submit" class="button_link bounty_action_button" value="(pursue)" />    
                                </form>
                              </td>
                              <td class="molecules_td molecules_tdr molecules_tdl">
                              </td>';
                    }
               ?>
            </tr>
            <tr class="molecules_tr">
                <td class="molecules_td molecules_tdl molecules_tdr">Claimed:</td>
                <?php 
                    if($bdata['claimed']){
                        echo '<td class="molecules_td molecules_tdr molecules_tdl">Yes</td>
                              <td class="molecules_td molecules_tdr">Completion date:</td>
                              <td class="molecules_td molecules_tdr molecules_tdl">
                              '.parsetimestamp($bdata['date_claimed']).'</td>';
                    }else{
                        echo '<td class="molecules_td molecules_tdr molecules_tdl">No</td>';
                        if($bdata['pursued_by']==$_SESSION['username']){
                            echo '<td class="molecules_td molecules_tdr">
                                    <form action="../cgi-bin/claimbounty.py" method="POST">
                                        <input type="hidden" name="userid" value="'.$_SESSION['userid'].'" />
                                        <input type="hidden" name="bid" value="'.$bid.'" />
                                        <input type="hidden" name="token" value="'.$_SESSION['token'].'" />
                                        <input type="submit" class="button_link bounty_action_button" value="(claim)" />    
                                    </form>
                                   </td>';
                            echo '<td class="molecules_td molecules_tdr molecules_tdl"></td>';
                        }else{
                            echo '<td class="molecules_td molecules_tdr"></td>
                                  <td class="molecules_td molecules_tdr molecules_tdl"></td>';
                        }
                    }
                ?>
            </tr>
        </table>    
    </div>


    <div id="div_commentholder">
<?php
        $q = $dbconn->prepare("SELECT 
                                bountycommentid,bountycomment,dateadded,username 
                               FROM 
                                bountycomments c left join users u ON u.userid=c.authorid 
                               WHERE c.bountyid=:num 
                               ORDER BY dateadded");
        $q->bindParam(":num",$bid,PDO::PARAM_INT);
        $q->execute();
        $count=0;
        while($row=$q->fetch()){
            $count++;
            echo '<div id="div_molcomment_'.$count.'" class="div_molcommentblock">
                    <div class="div_molcomment_author" id="div_molcomment_author_'.$count.'">'
                        .$row['username'].':
                        <br/> ('.parsetimestamp($row['dateadded']).') 
                    </div>
                    <div class="div_molcomment_text">
                        '.str_replace("\r\n","<br />",htmlentities($row['bountycomment']))
                  .'</div>';
                if($row['username']==$_SESSION['username']){
                    echo '<div class="div_deletecomment" style="font-size:1.2em;">
                            <form action="../cgi-bin/removebountycomment.py" method="POST">
                                <input type="hidden" name="bid" value="'.$bid.'" />
                                <input type="hidden" name="userid" value="'.$_SESSION['userid'].'" />
                                <input type="hidden" name="bountycommentid" value="'.$row['bountycommentid'].'" />
                                <input type="hidden" name="token" value="'.$_SESSION['token'].'" />
                                <input type="submit" class="button_link" value="X   "/>
                            </form>
                          </div>';
                }
            echo '</div>';
        }
        if($count==0){
            echo '<br /><br />No comments.';
        }
?>

        <div id="div_addmolcomment">
            <form action="../cgi-bin/addbountycomment.py" method="post">
            <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />
            <input type="hidden" name="bid" value="<?php echo $bid;?>" />
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
            <textarea name="textarea_addbountycomment" id="textarea_addmolcomment"></textarea><br />
            <input type="submit" id="commentbutton" value="Add Comment" />
            </form>
        </div>
    </div>
</div>
</div>
</body>
</html>
