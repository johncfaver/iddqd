<?php

//Perform search in database. Place resulting molids into session variable.
//Send user to displaysearch.php with results in $_SESSION['search_result'] array.

//The search_result session variable is a numeric array of molids, 
//       unless a similarity search was performed. In this case, 
//       it is an associative array of
//       molid=>similarity

    require('config.php');
    try{
        $dbconn = new PDO("pgsql:dbname=$dbname;host=$dbhost;port=$dbport",$dbuser,$dbpass);
    }catch(PDOException $e){
       // echo 'Database connection failed: '. $e->getMessage();
    }
    $dbconn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING );
  
    try{
        $nproc=exec("nproc");
    }catch(Exception $e){
        $nproc = 1;
    }

    session_start();
    if(!isset($_SESSION['username'])) returnhome(0);

    if(isset($_SESSION['search_results'])) unset($_SESSION['search_results']);

    //Max number of results desired. This limits our search. 0 implies no limit.
    $nummol=(isset($_POST['nummol']) and $_POST['nummol']>0)?(int)$_POST['nummol']:0; 

//Query parameters-constants
    //Max difference between query weight and molweight to count as a match.
    $arbitrary_weight_cutoff = 50;
    //Max Tanimoto coefficient for structure similarity search.
     $similaritythreshold=(isset($_GET['similaritythreshold']))?(real)$_GET['similaritythreshold']:0.3;
//Query parameters-input
    $query_molname  =(isset($_POST['query_molname']))?pg_escape_string($_POST['query_molname']):false;
    $query_molweight=(isset($_POST['query_molweight']))?pg_escape_string($_POST['query_molweight']):false;
    $query_target = (isset($_POST['query_targetid']))?(int)$_POST['query_targetid']:false;
//Parse molweight query, looking for range queries e.g. 500-560.
    if($query_molweight){
        $hyphenpos = strpos($query_molweight,'-');
        $query_molweight_range = ($hyphenpos!==false);  //IS this a range query?
        if($query_molweight_range){
            $query_molweight_lower= (int)substr($query_molweight,0,$hyphenpos);
            $query_molweight_upper= (int)substr($query_molweight,$hyphenpos+1);
            if($query_molweight_upper==0){
                $query_molweight_upper=100000;
            }
        }else{
            $query_molweight= (int)$query_molweight;
        }
    }else{
        $query_molweight_range = false;
    }
//Structure search  
    //molecular structure 
    $moltext=(isset($_POST['moltext']))?explode("\n",str_replace("\r",'',$_POST['moltext'])):false; 
    //Is there a non-empty structure for structure search?
    $query_structure=($moltext && count($moltext)>6)?true:false; 
    $searchtype=(isset($_POST['searchtype']))?$_POST['searchtype']:0;
    //Array of molids to be included in structure search.
    $structure_search_molids = Array();

//**************SEARCH PART*******************************************************
//Structure searching is the slowest part, so we will save that for last. First we'll filter by queried metadata like molname/molweight.
//Let's grab the molids that match the metadata
    if($query_molname or $query_molweight or $query_target){   
        if($query_target){
            $qstr = 'SELECT DISTINCT d.molid FROM moldata d LEFT JOIN molecules m ON m.molid=d.molid WHERE d.targetid=';
            $qstr.= $query_target;
        }else{
            $qstr = 'SELECT DISTINCT m.molid FROM molecules m WHERE molname IS NOT NULL';    
        }
        if($query_molname){
            $qstr.=' AND (m.molname ilike :str1 OR m.iupac ilike :str1 OR m.cas ilike :str1) ';
        }
        if($query_molweight_range){
            $qstr.=' AND m.molweight BETWEEN :num1 and :num2 ';
        }        
        if(!$query_molweight_range && $query_molweight){
            $qstr.=' AND abs(m.molweight-:num3) < '.$arbitrary_weight_cutoff;
        }
        if($nummol){
            $qstr.=' LIMIT :num4 ';
        }
            
        $q = $dbconn->prepare($qstr); 
        if($query_molweight_range){
            $q->bindParam(":num1",$query_molweight_lower,PDO::PARAM_INT);
            $q->bindParam(":num2",$query_molweight_upper,PDO::PARAM_INT);
        }
        if(!$query_molweight_range and $query_molweight){
            $q->bindParam(":num3",$query_molweight,PDO::PARAM_INT);
        }
        if($query_molname){
            $q->bindParam(":str1",$query_molname,PDO::PARAM_STR);
        }
        if($nummol){
            $q->bindParam(":num4",$nummol,PDO::PARAM_INT);
        }

        $q->execute();
        $err = $q->errorInfo();
        while($r = $q->fetch(PDO::FETCH_ASSOC)){
            array_push($structure_search_molids,$r['molid']);
        }
    }else{
        $q = $dbconn->query("SELECT DISTINCT molid FROM molecules");
        while($r = $q->fetch(PDO::FETCH_ASSOC)){;
            array_push($structure_search_molids,$r['molid']);
        }
    }

    if($query_structure){
      //Number of structures to search
        $nmolsearch = count($structure_search_molids);
      //Number of worker processes
        $nworkers = $nproc-1; 
      //Number of comparisons per worker; divide comparisons equally amongst workers, but cap at maxperjob
        $nmolperworker = ceil($nmolsearch/$nworkers);
        $maxperjob = 500;
        if($nmolperworker > $maxperjob){
            $nmolperworker = $maxperjob;
        }
        $njobs = ceil($nmolsearch/$nmolperworker);
        
      //Save query structure to /tmp
        $tempmolfile='/tmp/'.session_id().'.mol';  
        $fhandle = fopen($tempmolfile,'w');
        for($i=0;$i<count($moltext);$i++){
            fwrite($fhandle,$moltext[$i]."\n"); 
        }
        fclose($fhandle);

        if($searchtype=='similarity'){
            $parray = Array();
            $oarray = Array();
            $similarities = Array();
            while(count($oarray)<$njobs){
                $ijob = count($oarray);
                for($i=$ijob;$i<($ijob+$nworkers);$i++){
                    if($i>=$njobs) break;
                    $cmdstr = 'for i in';
                    for($j=$i*$nmolperworker;$j<($i+1)*$nmolperworker;$j++){
                        if($j>=$nmolsearch) break;
                        $cmdstr.=' '.$structure_search_molids[$j];
                    }
                    $cmdstr.=';do '.$babeldir.'babel uploads/structures/$i.mol '.$tempmolfile.' -ofpt 2>/dev/null |';
                    $cmdstr.=" grep Tanimoto | awk -v m=\$i '{if(NF>5 && $6>".$similaritythreshold.")print m\" \"$6}';done";
                    array_push($parray,popen($cmdstr,'r'));
                }
                for($i=$ijob;$i<($ijob+$nworkers);$i++){
                    if($i>=$njobs) break;
                    array_push($oarray,stream_get_contents($parray[$i]));
                }
            }
            //$oarray has each element as a job array, with each element a string of 'molid simililarity'
            for($i=0;$i<$njobs;$i++){
                $rlist = explode("\n",$oarray[$i]);
                foreach($rlist as $r){
                    $t = explode(" ",$r);
                    if(count($t)>1){
                        $similarities[$t[0]]=round($t[1],2);
                        if($nummol and count($similarities>=$nummol)) break;
                    }
                }
                if($nummol and count($similarities>=$nummol)) break;
            }
            //$similarities now is an associative array with molid=>similarity.
            $_SESSION['search_results']=$similarities;
            header('Location: displaysearch.php?similaritysearch=1');
            exit;
        }elseif($searchtype=='substructure'){
            $superstructures= Array(); 
            $parray = Array();
            $oarray = Array();
            while(count($oarray)<$njobs){
                $ijob = count($oarray);
                for($i=$ijob;$i<($ijob+$nworkers);$i++){
                    if($i>=$njobs) break;
                    $cmdstr = 'for i in';
                    for($j=$i*$nmolperworker;$j<($i+1)*$nmolperworker;$j++){
                        if($j>=$nmolsearch) break;
                        $cmdstr.=' '.$structure_search_molids[$j];
                    }
                    $cmdstr.=';do echo -n $i" "; '.$babeldir.'babel uploads/structures/$i.mol -s'.$tempmolfile.' /tmp/'.session_id().$i.'results.smi -xt 2>/dev/null;';
                    $cmdstr.='[ -s /tmp/'.session_id().$i.'results.smi ] && echo 1 || echo 0;done';
                    array_push($parray,popen($cmdstr,'r'));
                }
                for($i=$ijob;$i<($ijob+$nworkers);$i++){
                    if($i>=$njobs) break;
                    array_push($oarray,stream_get_contents($parray[$i]));
                }
            }
            for($i=0;$i<$njobs;$i++){
                $rlist = explode("\n",$oarray[$i]);
                foreach($rlist as $r){
                    $t = explode(" ",$r);
                    if(count($t)>1 and $t[1]=='1'){
                        array_push($superstructures,$t[0]);
                        if($nummol and count($superstructures)>=$nummol) break;
                    }
                }
                if($nummol and count($superstructures)>=$nummol) break;
            }
            //$superstructures now has every element as a molid which is a substructure of the query.
            $_SESSION['search_results'] = $superstructures;
        }
    }else{
        $_SESSION['search_results'] = $structure_search_molids;
    }
    header('Location: displaysearch.php');
?>
