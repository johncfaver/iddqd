<?php
    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);    
    }catch(PDOException $e){
       // echo 'Database connection failed: '. $e->getMessage();
    }
    session_start();
    $loggedin = (isset($_SESSION['username']));
?>
<!DOCTYPE html>
<html>
<head>
    <title>IDDQD - Main</title>
    <link rel="stylesheet" href="reset.css" type="text/css" />
    <link rel="stylesheet" href="iddqd.css" type="text/css" />
    <script type="text/javascript" src="iddqd.js"></script>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>
<div id="div_holder">

<div id="div_left">
    <div id="left_links">
<?php
    if($loggedin){
        echo '<span class="nonlinks">';
        if ($_SESSION['isadmin']) echo '<a href="admin.php" style="color:white">Administration</a><br/><br/>';
        echo '<a href="index.php" style="color:#bbbbff">Home</a><br /><br />';
        echo '<a href="search.php" style="color:white">Search</a> <br /><br />';
        echo '<a href="molecules.php" style="color:white">View Library</a><br /><br />';
        echo '<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />';
        echo '<a href="bounties.php" style="color:white">Bounties</a><br /><br />';
        echo '<a href="targets.php" style="color:white">Targets</a><br /><br />';
        echo '<a href="help.php" style="color:white">Help</a><br /><br />';
        echo '</span>';
    }
?>
    </div>
</div>    
<div id="div_top">
<?php
        if($loggedin){
            echo '<div id="div_notebook">
                    <a href="notebook.php">My Notebook: '.count($_SESSION['notebook_molids']).'</a>
                  </div>
                  <div id="div_login">
                    <span id="span_loggedin">
                        Logged in as '.$_SESSION['username'].' <a href="logout.php">(logout)</a>
                    </span>';
        }else{        
            echo '<div id="div_login">';
            //echo '<a href="registerpage.php" style="float:left;font-size:0.8em;">Create Account</a>';
            echo '<form id="login" method="post" action="login.php">
                    <input type="text" value="username" name="enteredusername" id="enteredusername" onclick="clearuserbox();" size="10" maxlength="20" required />
                    <input type="password" name="enteredpassword" value="password" id="enteredpassword" onclick="clearpasswordbox();" size="10" required />
                    <input type="submit" value="Log in"/>
                  </form>';
            echo '<a href="changepasswordrequestpage.php" style="position:absolute;bottom:-30px;left:120px;font-size:0.8em;z-index:2;">Retrieve Password</a>';

         }
         if(isset($_GET['status'])){
            if($_GET['status']=='badpw'){
                echo '<span style="font-size:1.8em;position:fixed;top:130px;left:530px;margin:0px;">
                        Invalid username/password.
                      </span>';
            }
         }
         if(isset($_GET['errorcode'])){
                echo '<span style="font-size:1.8em;position:fixed;top:130px;left:560px;margin:0px;">
                Error '.(int)htmlentities($_GET['errorcode']).' occurred.    
                </span>';
         }
        echo '</div>';
?>    
</div>
<div id="div_main">
<?php
        if(!isset($_GET['status']) and !isset($_GET['errorcode'])){
            echo '<span style="position:absolute;top:200px;left:300px">
                    Inhibitor Discovery, Design, and Quantification Database
                  </span>';
        }
?>
</div>
</div>
</body>
</html>
