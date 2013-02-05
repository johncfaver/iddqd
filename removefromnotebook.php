<?php
// Remove molid from notebook, by removing it from the session variable "notebook_molids"
// Go to either the notebook page or the molecule page depending on the GET variable "dest"
	require('/home/faver/bin/cred.php');
	if(isset($_GET['molid'])){
		$thismolid=(int)pg_escape_string($_GET['molid']);	
	}else{
		returnhome();
	}
	session_start();
	$ids=explode(',',$_SESSION['notebook_molids']);
	array_pop($ids);
	if(array_search($thismolid,$ids)>=0){
		$newidstr='';
		foreach($ids as $i){
			if($i==$thismolid){
				continue;
			}else{
				$newidstr.=$i.',';
			}
		}
		$_SESSION['notebook_molids']=$newidstr;
	}
	if(isset($_GET['dest'])){
		if($_GET['dest']=='nb'){
			header('Location: notebook.php');	
		}
	}else{	
		header('Location: viewmolecule.php?molid='.$thismolid);
	}
?>
