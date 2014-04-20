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
        }else{
            header('Location: index.php?errorcode='.$errorcode);	
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
