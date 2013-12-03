<?php
    /* This file crawls through the molids, and generates images for them sequentially.
    */
    $thismolid=isset($_GET['molid'])?(int)pg_escape_string($_GET['molid']):0;
    
    //If this molid structure doesn't exist or if we're past 1000, skip to next molid in sequence
    if(!file_exists('uploads/structures/'.$thismolid.'.mol') and $thismolid<1000){
        header("Location: ./pngwriter.php?molid=".($thismolid+1));
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="ChemDoodleWeb/install/ChemDoodleWeb.css" type="text/css">
<link rel="stylesheet" href="iddqd.css" type="text/css" />
<link rel="stylesheet" href="viewmolecule.css" type="text/css" />
<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb-libs.js"></script>
<script type="text/javascript" src="ChemDoodleWeb/install/ChemDoodleWeb.js"></script>
<script type="text/javascript" src="iddqd.js"></script>
<script type="text/javascript" src="viewmolecule.js"></script>
<script type="text/javascript">

    //On body load, we'll grab the molecule image, put it in the form, then submit the form
    function getmolecule(){
        document.getElementById("molfig").value=document.getElementById("viewerCanvas").toDataURL("image/png");
        document.getElementById("canvas2png").submit();
    }

</script>
</head>
<body onload="getmolecule()">

    <script type="text/javascript">
            var viewerCanvas = new ChemDoodle.ViewerCanvas('viewerCanvas', 400, 200);
            viewerCanvas.specs.bonds_width_2D = 1.0;
            viewerCanvas.specs.bonds_saturationWidth_2D = .18;
            viewerCanvas.specs.atoms_font_size_2D = 12;
            viewerCanvas.specs.atoms_font_families_2D = ["Helvetica", "Arial", "sans-serif"];
            viewerCanvas.specs.atoms_displayTerminalCarbonLabels_2D = true;
            var molfile='<?php
                        $fileContents=file_get_contents('uploads/structures/'.$thismolid.'.mol');
                            if($fileContents){
                                echo str_replace(array("\r\n", "\n", "\r", "'"), array("\\n", "\\n", "\\n", "\\'"), $fileContents);
                            }?>';
            var thismol = ChemDoodle.readMOL(molfile);
            thismol.scaleToAverageBondLength(20.0);
            viewerCanvas.loadMolecule(thismol);
            var t = document.getElementById('viewerCanvas');
            t.setAttribute('style','border:0px');
    </script>
    <form id="canvas2png" enctype="multipart/form-data" action="../cgi-bin/canvas2png.py" method="POST">
        <input type="hidden" name="molid" value="<?php echo $thismolid;?>" />
        <input type="hidden" name="molfig" id="molfig" value="default" />
    </form>    

</body>
</html>
