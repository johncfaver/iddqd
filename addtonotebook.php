<?php
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
		if($_GET['dest']=='nb'){
			header('Location: notebook.php');	
		}
	}else{	
		header('Location: viewmolecule.php?molid='.$addmolid);
	}
?>
