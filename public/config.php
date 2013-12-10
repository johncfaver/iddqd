<?php
//Read config file then load data.
    $configfile = file_get_contents("../config/iddqd-config.json");
    $config = json_decode($configfile,true); 
    $dbname=$config['postgresql']['database'];
	$dbhost=$config['postgresql']['host'];
	$dbport=$config['postgresql']['port'];
	$dbuser=$config['postgresql']['user'];
	$dbpass=$config['postgresql']['pass'];
    $babeldir=$config['babeldir'];

//Various constants. We could also read from database, but this is probably faster.
	$bindingdatatypes   = Array('IC50','EC50','kd');
	$bindingdataids     = Array('1','2','3');
	$propertydatatypes  = Array('CC50','Aq. Solubility');
	$propertydataids    = Array('4','5');
	$docdatatypes       = Array('H NMR','C NMR','Mass Spec.','Synthesis','Manuscript','Structure','Image','Other');
	$docdataids	        = Array('6','7','8','9','10','11','13','15');
	$datatypefromid     = Array('1'=>'IC50','2'=>'EC50','3'=>'kd',
				            '4'=>'CC50','5'=>'Aq. Solubility',
				            '6'=>'H NMR','7'=>'C NMR','8'=>'Mass Spec.','9'=>'Synthesis','10'=>'Manuscript',
			      	        '11'=>'Structure', '13'=>'Image', '15'=>'Other');

//Convert timestamp to MM/DD/YYYY
	function parsetimestamp($timestamp){
        if($timestamp){
		    return substr($timestamp,5,2).'/'.substr($timestamp,8,2).'/'.substr($timestamp,0,4);
        }else{
            return '';
        }
	}

//Send back to index.php
	function returnhome($errorcode){
        $errorcode = (int)$errorcode;
        if($errorcode==0){
		    header('Location: index.php');	
        }else({
            header('Location: index.php&errorcode='.$errorcode);	
        }
        exit;
	}

//Convert "C1H4" into "CH<sub>4</sub>", for example
	function parseformula($f){
		$ostr='';
		$f=str_split($f);
		for($i=0;$i<sizeof($f);$i++){
			if($i>0 and is_numeric($f[$i])){
				if(!is_numeric($f[$i-1])){
					$ostr.='<sub>';
				}
				$ostr.=$f[$i];
			}else{
                $olength = strlen($ostr);
				if($olength > 1){
					if(is_numeric($ostr[$olength-1])){
						$ostr.='</sub>'.$f[$i];
					}else{
						$ostr.=strtolower($f[$i]);
					}
				}else{
					if($i==0){
						$ostr.=$f[0];
					}else{
						$ostr.=strtolower($f[1]);
					}
				}
			}
		}
		$ostr.='</sub>';
        $formula = str_replace("<sub>1</sub>","",$ostr);
		return $formula;

	}

?>
