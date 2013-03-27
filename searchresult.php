<?php
	$debug=0;
        require('/home/faver/bin/cred.php');
        try{
                $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);
        }catch(PDOException $e){
                echo 'Connection failed: '. $e->getMessage();
        }
	$dbconn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING );
        session_start();
        $loggedin = (isset($_SESSION['username']))?True:False;
        if(!$loggedin) returnhome();

	$nummol=(isset($_POST['nummol']) and $_POST['nummol']>0)?(int)$_POST['nummol']:8;
	$molstart=(isset($_GET['molstart']))?(int)$_GET['molstart']:0;
	$sortby=(isset($_GET['sortby']))?pg_escape_string($_GET['sortby']):'dateadded';
	$sortdir=(isset($_GET['sortdir']))?(int)$_GET['sortdir']:0;
	$similaritythreshold=(isset($_GET['similaritythreshold']))?(real)$_GET['similaritythreshold']:0.3;
	
	$q = $dbconn->query("SELECT MIN(molid),MAX(molid),COUNT(molid) FROM molecules");
	$r = $q->fetch();
	$dbminmol = $r['min'];
	$dbmaxmol = $r['max'];
	$dbcountmol = $r['count'];
	
        $query_molname  =(isset($_POST['query_molname']))?pg_escape_string($_POST['query_molname']):0;
        $query_molweight=(isset($_POST['query_molweight']))?pg_escape_string($_POST['query_molweight']):0;
	$moltext=(isset($_POST['moltext']))?explode("\n",str_replace("\r",'',$_POST['moltext'])):0;
	$query_structure=($moltext && count($moltext)>6)?1:0;
	if($query_structure){
		$tempmolfile='/tmp/'.session_id().'.mol';
		$fhandle = fopen($tempmolfile,'w');
		for($i=0;$i<count($moltext);$i++){
			fwrite($fhandle,$moltext[$i]."\n"); 
		}
		fclose($fhandle);
		$searchtype=(isset($_POST['searchtype']))?$_POST['searchtype']:0;
		if($searchtype=='similarity'){
			$similarities = Array();
			$cmdstr='for i in $(seq '.$dbminmol.' 1 '.$dbmaxmol.');do /usr/bin/babel uploads/structures/$i.mol '.$tempmolfile.' -ofpt 2>/dev/null | ';
			$cmdstr.='grep Tanimoto | tr \'\n\' \' \0\' && ';
			$cmdstr.='echo $i;done | awk \'{if(NF>1 && $6>'.$similaritythreshold.'){print $7" "$6}}\' | sort -rnk 2';
			exec($cmdstr,$similarities);	 //$similarities has strings like : 'molid similarity'
		}elseif($searchtype=='substructure'){
			$superstructures= Array(); 
			for($i=$dbminmol;$i<=$dbmaxmol;$i++){
				$cmdstr='/usr/bin/babel uploads/structures/'.$i.'.mol -s'.$tempmolfile.' /tmp/'.session_id().'results.smi -xt 2>/dev/null;';
				$cmdstr.='[ -s /tmp/'.session_id().'results.smi ] && echo 1';
				if(exec($cmdstr)){
					array_push($superstructures,$i);	
					if(count($superstructures)>$nummol){
						break;
					}
				}
			}
		}
	}
        if(substr_count($query_molweight,'-')==1){
                $query_molweight_range= strpos($query_molweight,'-')+1;
                $query_molweight_lower= (int)substr($query_molweight,0,$query_molweight_range-1);
                $query_molweight_upper= (int)substr($query_molweight,$query_molweight_range);
		if($query_molweight_upper==0){
			 $query_molweight_upper=10000;
		}
        }else{
                $query_molweight_range=0;
		$query_molweight= (int)$query_molweight;
        }
        $query_target = (isset($_POST['query_targetid']))?(int)$_POST['query_targetid']:0;
?>
<!DOCTYPE html>
<html>
<head>
<title>Search Result</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="iddqd.js"></script>
<link rel="stylesheet" href="iddqd.css" type="text/css" />
</head>
<body>

<div id="div_left">
	<div id="left_links">
		<span class="nonlinks">
		<a href="index.php" style="color:white">Home</a><br /><br />
		<a href="search.php" style="color:#bbbbff">Search</a> <br /><br />
		<a href="molecules.php" style="color:white">View Molecules</a><br /><br />
		<a href="addmolecule.php" style="color:white">Add Molecules</a><br /><br />
		<a href="targets.php" style="color:white">View Targets</a><br /><br />
		<a href="help.php" style="color:white">Help</a><br /><br />
	</span>
	</div>
	<div id="div_ad">
		<a href="http://web.chemdoodle.com"><img src="chemdoodleweb.png" /></a>
	</div>
</div>	
<div id="div_top">
	<div id="div_notebook">
		<a href="notebook.php">My Notebook: <?php echo count($_SESSION['notebook_molids']); ?></a>
	</div>
	<div id="div_login">
		<span id="span_loggedin">Logged in as <?php echo $_SESSION['username'];?><a href="logout.php">(logout)</a></span>
	</div>	
</div>
<div id="div_main">
<?php
	/*if($molstart>=$nummol){
		echo '<div id="div_molecules_prev"><span class="nonlinks"><a href="molecules.php?molstart='.($molstart-$nummol).'&sortby='.$sortby.'"> << previous </a></span></div>';
	}
	if($molstart+$nummol<$dbcountmol){
		echo '<div id="div_molecules_next"><span class="nonlinks"><a href="molecules.php?molstart='.($molstart+$nummol).'&sortby='.$sortby.'"> next>></a></span></div>';
	}*/
	echo '<span style="font-size:0.7em;">';
	echo 'Searched '.$dbcountmol.' molcules for ';
	if($query_target){
		echo 'target:'.$query_target;
	}
	if($query_molname){
		echo ' name:'.$query_molname;
	}
	if($query_molweight){
		echo ' molweight:'.$query_molweight;
	}
	if($query_structure){
		echo ' structure:';
		if($searchtype){
			echo $searchtype;
		}
	}
	echo '</span>';
?>
<?php
	if($query_target){
		$qstr = 'SELECT distinct m.molid,m.molname,m.dateadded,u.username,m.molweight,abs(m.molweight-:num5) from moldata d join molecules m on m.molid=d.molid join users u on u.userid=m.authorid where targetid=';
		$qstr.= $query_target;
	}else{
		$qstr = 'SELECT distinct m.molid,m.molname,m.dateadded,u.username,m.molweight,abs(m.molweight-:num5) from molecules m join users u on u.userid=m.authorid';	
	}
	if($query_molname){
		$qstr.=' and (m.molname ilike :str1 or m.iupac ilike :str1 or m.cas ilike :str1) ';
	}
	if($query_molweight_range){
		$qstr.=' and m.molweight between :num3 and :num4 ';
	}		
	if($query_structure && $searchtype=='similarity'){
		$qstr.=' and m.molid in (';
		for($i=0;$i<count($similarities)-1;$i++){
			$t = explode(' ',$similarities[$i]);
			$qstr.=$t[0].',';
		}
		$t = explode(' ',$similarities[count($similarities)-1]);
		$qstr.=$t[0].') ';
	}
	if($query_structure && $searchtype=='substructure'){
		if(count($superstructures)>0){
			$qstr.= ' and m.molid in (';
			for($i=0;$i<count($superstructures)-1;$i++){
				$qstr.=$superstructures[$i].',';
			}
			$qstr.=$superstructures[count($superstructures)-1].') ' ;
		}else{
			$qstr.=' and m.molid < 0 ';
		}
	}


	if(!$query_molweight_range && $query_molweight){
		$qstr.=' order by abs(m.molweight-:num5) ';
	}
	$qstr.=' limit :num1 offset :num2';
	$q = $dbconn->prepare($qstr); 
	$q->bindParam(":num1",$nummol,PDO::PARAM_INT);
	$q->bindParam(":num2",$molstart,PDO::PARAM_INT);
	if($query_molweight_range){
		$q->bindParam(":num3",$query_molweight_lower,PDO::PARAM_INT);
		$q->bindParam(":num4",$query_molweight_upper,PDO::PARAM_INT);
		$q->bindParam(":num5",$nummol,PDO::PARAM_INT);
	}elseif($query_molweight){
		$q->bindParam(":num5",$query_molweight,PDO::PARAM_INT);
	}else{
		$q->bindParam(":num5",$nummol,PDO::PARAM_INT);
	}
	if($query_molname){
		$mn = '%'.$query_molname.'%';
		$q->bindParam(":str1",$mn,PDO::PARAM_STR);
	}

	$q->execute();
	$err = $q->errorInfo();
	$response=$q->fetchAll(PDO::FETCH_ASSOC);
if(count($response)>0){
	echo '<table id="moleculetable" >
		<tr class="moltr">
		<th class="molth">Structure </th>';
	if($query_structure && $searchtype=='similarity') echo '<th class="molth">Similarity</th>';
	echo '<th class="molth">Name </th>
		<th class="molth">MW </th>
		<th class="molth">Author</th>
		<th class="molth">Date </tr>
		</tr>';
	
	$count=0;
	if($query_structure && $searchtype=='similarity'){
		for($i=$molstart;$i<count($similarities);$i++){
			if($i-$molstart > $nummol){
				 break;
			}
			if($i%2==1){
				$tdcolor="moltd2";
			}else{
				$tdcolor="moltd1";
			}
			$t = explode(' ',$similarities[$i]);
			$smolid = $t[0];
			$ssimilarity = $t[1];
			for($j=0;$j<count($response);$j++){
				if($response[$j]['molid']==$smolid){
					echo '<tr class="moltr">';
					echo '<td class="'.$tdcolor.'">';
					echo '<a href="viewmolecule.php?molid='.$response[$j]['molid'].'">';
					echo '<img src="uploads/sketches/'.$response[$j]['molid'].'.jpg" style="height:60px"/></a>';
					echo '</td>';
	
					echo '<td class="'.$tdcolor.'">';
					echo $ssimilarity;
					echo '</td>';		
		
					echo '<td class="'.$tdcolor.'">';
					echo '<a href="viewmolecule.php?molid='.$response[$j]['molid'].'">'.$response[$j]['molname'].'</a>';
					echo '</td>';
	
					echo '<td class="'.$tdcolor.'">';
					echo $response[$j]['molweight'].'</a>';
					echo '</td>';
	
					echo '<td class="'.$tdcolor.'">';
					echo $response[$j]['username'];
					echo '</td>';
	
					echo '<td class="'.$tdcolor.'">';
					echo parsetimestamp($response[$j]['dateadded']);
					echo '</td>';
					echo '</tr>';	
				}
			}
			$count++;
		}
	}else{
		foreach($response as $entry){
			if($count%2==1){
				$tdcolor="moltd2";
			}else{
				$tdcolor="moltd1";
			}
			echo '<tr class="moltr">';
				echo '<td class="'.$tdcolor.'">';
					echo '<a href="viewmolecule.php?molid='.$entry['molid'].'"><img src="uploads/sketches/'.$entry['molid'].'.jpg" style="height:60px"/></a>';
				echo '</td>';
					
				echo '<td class="'.$tdcolor.'">';
					echo '<a href="viewmolecule.php?molid='.$entry['molid'].'">'.$entry['molname'].'</a>';
				echo '</td>';
	
				echo '<td class="'.$tdcolor.'">';
					echo $entry['molweight'].'</a>';
				echo '</td>';
	
				echo '<td class="'.$tdcolor.'">';
					echo $entry['username'];
				echo '</td>';
	
				echo '<td class="'.$tdcolor.'">';
					echo parsetimestamp($entry['dateadded']);
				echo '</td>';
			echo '</tr>';	
			$count++;
		}	
	}
	echo '</table>';
}else{
	echo '<br /><br /><br /><br />No results.';
}

	if($debug){
		echo '<span style="position:fixed;right:100px;top:400px;z-index:2;background:white;">';
		echo $cmdstr.' <br />';
		print_r($superstructures);
		echo "\n\n";
		echo $qstr;
		echo "\n\n";
		print_r($similarities);
		echo "\n\n";
		echo $count.' '.count($similarities);
		echo '</span>';
	}
?>
</div>
</body>
</html>
