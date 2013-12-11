<?php
/*
	Given a molid, add the molecule to the notebook_molids session array.
    Given a targetid, add all associated molecules to notebook_molids session array.
	Return the user to a specified destination, or default to viewmolecule page.
*/
	require('config.php');
    session_start();

	if(isset($_GET['molid'])){
		$addmolid=(int)pg_escape_string($_GET['molid']);	
	    if(!in_array($addmolid,$_SESSION['notebook_molids'])){
		    array_push($_SESSION['notebook_molids'],$addmolid);
	    }
	}elseif(isset($_GET['targetid'])){
        $addtargetid = (int)pg_escape_string($_GET['targetid']);
        try{
            $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);    
        }catch(PDOException $e){
            header("Location: index.php?errorcode=40");
            exit;
        }
        //Select only inhibitors with experimental data.
        $q = $dbconn->prepare("SELECT DISTINCT molid FROM moldata WHERE targetid=:num "); 
        $q->bindParam(":num",$addtargetid,PDO::PARAM_INT);
        $q->execute();
        while($r = $q->fetch(PDO::FETCH_NUM)){
            if(!in_array($r[0],$_SESSION['notebook_molids'])){
		        array_push($_SESSION['notebook_molids'],$r[0]);
	        }
        }
    }else{
		returnhome(2);
	}
	

	if(isset($_GET['dest'])){
		if($_GET['dest']=='nb'){	    // Intended destination is notebook.php
			header('Location: notebook.php');	
		}elseif($_GET['dest']=='vm'){   // Intended destination is viewmolecule.php
            header('Location: viewmolecule.php?molid='.$addmolid);
        }elseif($_GET['dest']=='vt'){   // Intended destination is viewtarget.php
            header('Location: viewtarget.php?targetid='.$addtargetid);
        }
	}else{	//notebook.php is the default destination.
	    header('Location: notebook.php');	
	}
    exit;
?>
