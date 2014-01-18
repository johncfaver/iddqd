<?php

//registerpage.php
//email invitations link here when new users are invited to register
//Need to verify inviation key, which is in the URL sent in the invitation email
//User generates a username and password.
//Info sent to register.php

    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);    
    }catch(PDOException $e){
       // echo 'Database connection failed: '. $e->getMessage();
    }

    $key = (isset($_GET['invitekey']))?pg_escape_string($_GET['invitekey']):0;
    if($key){
        $q = $dbconn->prepare("SELECT email FROM invites WHERE invitekey=:str1 AND datejoined IS NULL");
        $q->bindParam(":str1",$key,PDO::PARAM_STR);
        $q->execute();
        $r = $q->fetch(PDO::FETCH_ASSOC);
        if(sizeof($r) != 1){
            $key=0;
        }else{
            $email = $r['email'];
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
	<title>Register New Account</title>
	<link rel="stylesheet" href="reset.css" type="text/css" />
	<link rel="stylesheet" href="iddqd.css" type="text/css" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>
<div id="div_holder">
<div id="div_left"></div>	
<div id="div_top">
	<div id="div_login"></div>	
</div>
<div id="div_main">
<?php
    if(!$key){
        echo '<span style="position:absolute;top:100px;left:350px;">No valid key provided!</span>';
    }else{
    	if(isset($_GET['nameexists'])){
    		if((int)$_GET['nameexists']==1){
    			echo 'That username exists already.<br />';
    		}
    	}
        if(isset($_GET['usernameisbad'])){
            if((int)$_GET['usernameisbad']==1){
                echo 'That username was invalid. Try again using 3-20 valid characters.<br />';
            }
        }
        if(isset($_GET['emailisbad'])){
            if((int)$_GET['emailisbad']==1){
                echo 'That email was invalid. Try again.<br />';
            }
        }
        if(isset($_GET['passwordisbad'])){
            if((int)$_GET['passwordisbad']==1){
                echo 'That password was invalid. Try again using 5-20 valid characters.<br />';
            }
        }
	
        echo '<span id="span_regspan">
	            <form method="post" action="register.php" id="register" >
		            Username  
                    <input type="text" id="desiredusername" name="desiredusername" size="8" maxlength="20" required />  
                    <br />
                    <span style="font-size:0.7em;">
                        (3-20 valid characters)
                    </span>
                    <br /><br />
	                Email: &nbsp;&nbsp;  '.$email.'
                    <br />
                    <span style="font-size:0.7em;">
                        (for password recovery)
                    </span>
                    <br /><br />
	                Password: 
                    <input type="password" id="desiredpassword" name="desiredpassword" size="8" maxlength="20" required /> 
                    <br />
                    <span style="font-size:0.7em;">
                        (5-20 characters)
                    </span><br /><br />
                    <input type="hidden" name="invitekey" value="'.$key.'"/>
                    <input type="hidden" name="desiredemail" value="'.$email.'"/>
    	            <input type="submit" value="Register" />	
	            </form>
	          </span>';
    }
    ?>
</div>
</div>
</body>
</html>
