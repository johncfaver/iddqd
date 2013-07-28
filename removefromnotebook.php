<?php
/* Remove molid from notebook, by removing it from the session variable "notebook_molids"
   Go to either the notebook page or the molecule page depending on the GET variable "dest"
*/
	require('/home/faver/bin/cred.php');
	if(isset($_GET['molid'])){
		$thismolid=(int)pg_escape_string($_GET['molid']);	
	}else{
		returnhome();
	}
	session_start();

	if(in_array($thismolid,$_SESSION['notebook_molids'])){
		$i = array_search($thismolid,$_SESSION['notebook_molids']); 
		unset($_SESSION['notebook_molids'][$i]);
		$i = array_values($_SESSION['notebook_molids']);
		$_SESSION['notebook_molids']=$i;
	}
	if(isset($_GET['dest'])){
		if($_GET['dest']=='nb'){
			header('Location: notebook.php');	
            exit;
		}
	}else{	
		header('Location: viewmolecule.php?molid='.$thismolid);
        exit;
	}
?>
