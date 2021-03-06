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
    if(!$_SESSION['isadmin']) returnhome(0);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Administration</title>
<link rel="stylesheet" href="reset.css" type="text/css" />
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<script type="text/javascript" src="iddqd.js"></script>
<script type="text/javascript">
function prepare_emails(){
    var out_textbox = document.getElementById("invitation_addresses");
    var in_textbox = document.getElementById("input_email_addresses");
    var input = encodeURIComponent(in_textbox.value);
    var xhr = new XMLHttpRequest();

    out_textbox.innerHTML='';
    xhr.onreadystatechange = function(){
        try{
            if(xhr.readyState === 4 && xhr.status === 200){
                out_textbox.innerHTML=xhr.responseText;
            }
        }catch(e){
        }
    };
    xhr.open("GET","../cgi-bin/ajax-checkemails.py?emailstr="+input,true);
    xhr.send();
}
</script>
</head>
<body>
<div id="div_shade_window"></div>
<div id="div_holder">
<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
            <?php if ($_SESSION['isadmin'])echo '<a href="admin.php" style="color:#bbbbff">Administration</a><br/><br/>';?>
	    	<a href="index.php" style="color:white">Home</a><br /><br />
	    	<a href="search.php" style="color:white">Search </a><br /><br />
	    	<a href="molecules.php" style="color:white">View Library</a><br /><br />
	    	<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
	        <a href="bounties.php" style="color:white">Bounties</a><br /><br />
	        <a href="targets.php" style="color:white">Targets</a><br /><br />
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

    <div style="width:300px;position:absolute;left:100px;top:30px;">Registered users:</div>  

    <table class="moleculetable" style="width:575px;left:0px;font-size:0.8em;">
        <tr class="moltr">
            <th class="molth moltdborderright">Username</th>
            <th class="molth moltdborderright">Email</th>
            <th class="molth moltdborderright">Admin</th>
            <th class="molth">Action</th>
        </tr>
<?php
        $qstr = 'SELECT username,userid,email,isadmin FROM users ORDER BY userid';
        $q = $dbconn->query($qstr,PDO::FETCH_ASSOC);
        foreach($q as $r){
            $tdcolor=($r['userid']==$_SESSION['userid'])?'moltdcolor':'';
            echo '<tr class="moltr" style="height:50px;">
                    <td class="moltd '.$tdcolor.' moltdborderright">
                        '.htmlentities($r['username']).'
                    </td>
                    <td class="moltd '.$tdcolor.' moltdborderright">
                        '.htmlentities($r['email']).'
                    </td>
                    <td class="moltd '.$tdcolor.' moltdborderright ">
                        '.(($r['isadmin'])?'Yes':'No').'
                    </td>
                    <td class="moltd '.$tdcolor.' ">';
            if(!$r['isadmin']){
                    echo '<form action="cgi-bin/createadmin.py" method="POST">
                            <input type="hidden" name="token" value="'.$_SESSION['token'].'"/>
                            <input type="hidden" name="userid" value="'.$_SESSION['userid'].'"/>
                            <input type="hidden" name="upgradeuserid" value="'.$r['userid'].'"/>
                            <input type="button" value="Promote" style="float:left;margin-left:20px;width:35%;font-size:1.0em" onclick="shadewindow();popnotes(\'div_createadmincheck_'.$r['userid'].'\');return false;" />
                            <div id="div_createadmincheck_'.$r['userid'].'" class="div_notespopup">
                                <span class="span_popup_main_text" style="font-size:1.1em;">
                                    Are you sure you want to promote this user? <br/><br/>They will be able to manage and invite users though this administration page.
                                </span>
                                <input type="submit" value="Continue" class="button_popup button_popup_left" />
                                <input type="button" value="Cancel" class="button_popup button_popup_right" onclick="closenotes(\'div_createadmincheck_'.$r['userid'].'\');unshadewindow();return false;"/>
                            </div>


                          </form>
                    ';
                    echo '<form action="cgi-bin/deleteuser.py" method="POST">
                            <input type="hidden" name="token" value="'.$_SESSION['token'].'"/>
                            <input type="hidden" name="userid" value="'.$_SESSION['userid'].'"/>
                            <input type="hidden" name="deleteuserid" value="'.$r['userid'].'"/>
                            <input type="button" value="Remove" style="float:right;margin-right:20px;width:35%;font-size:1.0em" onclick="popnotes(\'div_removeusercheck_'.$r['userid'].'\');shadewindow();return false;" />
                            <div id="div_removeusercheck_'.$r['userid'].'" class="div_notespopup" style="font-size:1.1em">
                                <span class="span_popup_main_text">
                                    Are you sure you want to remove this user? <br/><br/>Their data will remain but they will no longer be able to log in with this account. 
                                </span>
                                <input type="submit" value="Continue" class="button_popup button_popup_left" />
                                <input type="button" value="Cancel" class="button_popup button_popup_right" onclick="closenotes(\'div_removeusercheck_'.$r['userid'].'\');unshadewindow();return false;"/>
                            </div>

                          </form>';
            }
            if($r['userid']==$_SESSION['userid'] and $_SESSION['isadmin']){
                    echo '<form action="cgi-bin/forfeitadmin.py" method="POST">
                            <input type="hidden" name="token" value="'.$_SESSION['token'].'"/> 
                            <input type="hidden" name="userid" value="'.$_SESSION['userid'].'"/>
                            <input type="button" value="Forfeit Admin Status" style="margin:auto;font-size:1.0em;" onclick="popnotes(\'div_forfeitcheck_'.$r['userid'].'\');shadewindow();return false;" />
                            <div id="div_forfeitcheck_'.$r['userid'].'" class="div_notespopup" style="font-size:1.1em;">
                                <span class="span_popup_main_text">
                                    Are you sure you want to forfeit your admin status?
                                    <br/><br/>
                                    You won\'t be able to invite or manage users!
                                </span>
                                <input type="submit" value="Continue" class="button_popup button_popup_left" />
                                <input type="button" value="Cancel" class="button_popup button_popup_right" onclick="closenotes(\'div_forfeitcheck_'.$r['userid'].'\');unshadewindow();"/>
                            </div>
                          </form>';
            }
            echo '  </td>
                  </tr>';
        }
?>
    </table>
    
    
    <div style="width:350px;position:absolute;left:625px;top:30px;">
        Send Invitations by Email:
        <br/>
        <span style="font-size:0.8em;">
            (comma separated)
        </span>
    </div>  

    <div id="invitebox" style="position:absolute;width:350px;top:70px;left:625px;">
        <form action="cgi-bin/inviteusers.py" method="POST">
            <textarea id="input_email_addresses" name="email_addresses" style="width:350px;height:100px;" ></textarea>
            <br/><br/>
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
            <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>" />
            <input type="button" value="Prepare Email Invitations" style="position:absolute;left:75px;width:200px;" onclick="popnotes('email_dialog');prepare_emails();return false;" />
            <div id="email_dialog" class="div_notespopup">
                <span class="span_closenotespopup" onclick="closenotes('email_dialog');return false;"><a href="#">X</a></span>
                <span class="span_popup_main_text" style="top:20px;left:200px;">Invitations will be sent to:</span>
                <textarea id="invitation_addresses" class="textarea_notespopup" style="height:130px;width:500px;"  >
                </textarea>
                <input type="submit" value="Send Email Invitations" class="button_popup button_popup_right" style="width:200px;" />
            </div>
        </form>
    </div>


    <div style="width:300px;position:absolute;left:650px;top:275px;">
        Pending invitations:
        <br/>
    </div>  


    <table class="moleculetable" style="width:350px;left:630px;top:300px;font-size:0.8em;">
        <tr class="moltr">
            <th class="molth moltdborderright">Email</th>   
            <th class="molth moltdborderright ">Date</th>
            <th class="molth">Void Key</th>
        </tr>
<?php 
        $qstr = 'SELECT email, datesent FROM invites WHERE datejoined IS NULL';
        $q = $dbconn->query($qstr,PDO::FETCH_ASSOC);
        $count=0;
        foreach($q as $r){
            echo '<tr class="moltr">
                    <td class="moltd moltdborderright">
                        '.htmlentities($r['email']).'
                    </td>
                    <td class="moltd moltdborderright">
                        '.parsetimestamp($r['datesent']).'
                    </td>
                    <td class="moltd">
                        <a href="#" onclick="shadewindow();popnotes(\'div_void_key_'.$count.'\');return false;">
                            <img src="delete_icon.png" /> 
                        </a>
                        <div id="div_void_key_'.$count.'" class="div_notespopup" style="font-size:1.1em;">
                            <span class="span_popup_main_text">
                                Are you sure you want to void this invitation key? <Br/><br/>This person won\'t be able to register without a new invitation.
                            </span>
                            <form action="cgi-bin/voidkey.py" method="POST">
                                <input type="hidden" name="token" value="'.$_SESSION['token'].'"/> 
                                <input type="hidden" name="userid" value="'.$_SESSION['userid'].'"/>
                                <input type="hidden" name="inviteemail" value="'.$r['email'].'"/>
                                <input type="hidden" name="datesent" value="'.$r['datesent'].'"/>
                                <input type="submit" value="Continue" class="button_popup button_popup_left" />
                                <input type="button" value="Cancel" class="button_popup button_popup_right" onclick="closenotes(\'div_void_key_'.$count.'\');unshadewindow();return false;"/>
                            </form>
                        </div>
                    </td>
                  </tr>';
                  $count++;
        }
?>
    </table>

</div>
</div>
</body>
</html>
