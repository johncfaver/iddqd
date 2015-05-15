function switchdatadiv(selected){
	var t = document.getElementById("div_tabbindingdata");
	t.className="datatab";
	t = document.getElementById("div_tabdocdata");
	t.className="datatab";
	t = document.getElementById("div_tabmodelingdata");
	t.className="datatab";
	t = document.getElementById("div_tab"+selected);
	t.className="datatab datatabopen";

	t = document.getElementById("div_bindingdata");
	t.style.display="none";
	t = document.getElementById("div_docdata");
	t.style.display="none";
	t = document.getElementById("div_modelingdata");
	t.style.display="none";
	t = document.getElementById("div_"+selected);
	t.style.display="block";
}
function deletecheck(){
    var t = document.getElementById("div_shade_window");
    t.style.display = 'block';
    var t = document.getElementById("div_deletecheck");
    t.style.display = 'block';
}
function closedeletecheck(){
    var t = document.getElementById("div_deletecheck");
    t.style.display = 'none';
    var t = document.getElementById("div_shade_window");
    t.style.display = 'none';
}
function inhibitorEntry(molid,molname,activity,datatype){
    this.molid = molid;
    this.molname = molname;
    this.activity=activity;
    this.datatype = datatype;
    this.row='<td class="molecules_td molecules_tdl"><a href="viewmolecule.php?molid='+this.molid+'">'+this.molname+'</a></td>';
    this.row+='<td class="molecules_td molecules_tdr">'+this.activity+'</td>';
    this.row+='<td class="molecules_td">'+this.datatype+'</td>';
}
var inhibitors = new Array();
var inactives = new Array();
var actives = new Array();


var inhibitorstart=0;
function inhibitorPageRight(nmolperpage){
    inhibitorstart+=nmolperpage;
    if(inhibitorstart>=inhibitors.length){
        inhibitorstart=inhibitors.length-nmolperpage;
    }
    showInhibitors(inhibitorstart);
}
function inhibitorPageLeft(nmolperpage){
    inhibitorstart-=nmolperpage;
    if(inhibitorstart<0){
        inhibitorstart=0;
    }
    showInhibitors(inhibitorstart);
}
function showInhibitors(start){
    t = document.getElementById("bindingtable").rows;
    for(var i=1;i<t.length;i++){
        if(start+i-1 < inhibitors.length){
            t[i].innerHTML=inhibitors[start+i-1].row;
        }else{
            t[i].innerHTML='';
        }
    }
}
