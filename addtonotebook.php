<?php
/*
	Given a molid, add the molecule to the notebook_molids session array.
	Return the user to a specified destination, or default to viewmolecule page.
*/
	require('/home/faver/bin/cred.php');
	if(isset($_GET['molid'])){
		$addmolid=(int)pg_escape_string($_GET['molid']);	
	}else{
		returnhome();
	}
	session_start();

	if(!in_array($addmolid,$_SESSION['notebook_molids'])){
		array_push($_SESSION['notebook_molids'],$addmolid);
	}

	if(isset($_GET['dest'])){
		if($_GET['dest']=='nb'){	 // Intended destination is notebook.php
			header('Location: notebook.php');	
            exit;
		}
	}else{	
		header('Location: viewmolecule.php?molid='.$addmolid);
        exit;
	}
?>
