<?php
/* Remove molid from notebook, by removing it from the session variable "notebook_molids"
   Go to either the notebook page or the molecule page depending on the GET variable "dest"
*/
	require('config.php');
	if(isset($_GET['molid'])){
		$thismolid=(int)pg_escape_string($_GET['molid']);	
        $clearnotebook=false;
	}elseif(isset($_GET['all'])){
        $clearnotebook=(pg_escape_string($_GET['all'])=='1');
    }
    else{
		returnhome(9);
	}
	session_start();
    if(!$_SESSION['notebook_molids']) returnhome(0);

    if($clearnotebook){
        unset($_SESSION['notebook_molids']);
        $_SESSION['notebook_molids']=Array();
    }elseif(in_array($thismolid,$_SESSION['notebook_molids'])){
		$i = array_search($thismolid,$_SESSION['notebook_molids']); 
		unset($_SESSION['notebook_molids'][$i]);
		$i = array_values($_SESSION['notebook_molids']);
		$_SESSION['notebook_molids']=$i;
	}
	if(isset($_GET['dest'])){
		if($_GET['dest']=='nb'){
			header('Location: notebook.php');	
		}elseif($_GET['dest']=='vm'){
			header('Location: viewmolecule.php?molid='.$thismolid);	
		}
	}else{	
	    header('Location: notebook.php');	
	}
?>
