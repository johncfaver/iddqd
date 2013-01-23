function getmolecule(){
	document.getElementById("molfig").value=document.getElementById("sketcher").toDataURL("image/png");
	document.getElementById("moltext").value=ChemDoodle.writeMOL(sketcher.molecule);
}
function clearuserbox(){
	document.getElementById("enteredusername").value='';
}
function clearpasswordbox(){
	document.getElementById("enteredpassword").value='';
}
function opendatapopup(username,date,notes){
	var pop = document.getElementById("div_datapopup");
	var htmlstring='<span style="position:absolute;top:5px;left:10px;font-size:1.0em;">Submitted by '+username+' on '+date+':</span>';
	htmlstring+='<span style="position:absolute;top:35px;left:20px;font-size:0.9em;width:560px;height:190px;background:white;border:1px solid black">'+notes+' </span>';
	htmlstring+='<span class="span_closenotespopup" onclick="closedatapopup();"><a href="#">X</a></span>';
	pop.innerHTML=htmlstring;
	pop.style.display="block";
}
function closedatapopup(){
	var pop = document.getElementById("div_datapopup");
	pop.style.display="none";
}
function popnotes(field){
	var pop = document.getElementById(field);	
	pop.style.display="block";
}
function closenotes(field){
	var pop = document.getElementById(field);	
	pop.style.display="none";
}
function switchinputdatatab(selected){
	var t = document.getElementById("div_tab_datatype_bindingdata");
	t.style.background="#ffffff";
	t = document.getElementById("div_tab_datatype_propertydata");
	t.style.background="#ffffff";
	t = document.getElementById("div_tab_datatype_docdata");
	t.style.background="#ffffff";
	t = document.getElementById("div_tab_datatype_"+selected);
	t.style.background="#bbbbff";
	t = document.getElementById("div_input_datatype_bindingdata");
	t.style.display="none";
	t = document.getElementById("div_input_datatype_propertydata");
	t.style.display="none";
	t = document.getElementById("div_input_datatype_docdata");
	t.style.display="none";
	t = document.getElementById("div_input_datatype_"+selected);
	t.style.display="block";
}
function parsetimestamp(timestamp){
	var outstr = timestamp.substring(5,7)+'/';
	outstr+=timestamp.substring(8,10)+'/';
	outstr+=timestamp.substring(0,4);
	return oustr;
}
function morebindingdata(){
	var t = document.getElementById("bindingdatainputlines");
	num_bindingdata+=1;
	var i = num_bindingdata;
	
	var newinputline = document.createElement('div');		
	newinputline.setAttribute('id','div_bindingdata_new_'+i.toString());	
	newinputline.setAttribute('class','nonlinks');	

	var newtargetselect = document.createElement('select');
	newtargetselect.setAttribute('id','bindingdata_targetid_new_'+i.toString());
	newtargetselect.setAttribute('name','bindingdata_targetid_new_'+i.toString());
	for(var j=0;j<targetnames.length;j++){
		var newoption = document.createElement('option');
		newoption.setAttribute('value',targetids[j]);
		newoption.innerHTML=targetnames[j];
		newtargetselect.appendChild(newoption);
	}	
	newinputline.appendChild(newtargetselect);

	var newdatatypeselect = document.createElement('select');
	newdatatypeselect.setAttribute('id','bindingdata_datatypeid_new_'+i.toString());
	newdatatypeselect.setAttribute('name','bindingdata_datatypeid_new_'+i.toString());
	for(var j=0;j<bindingdatatypes.length;j++){
		var newoption = document.createElement('option');
		newoption.setAttribute('value',bindingdataids[j]);
		if(j==1){
			newoption.setAttribute('selected','selected');
		}
		newoption.innerHTML=bindingdatatypes[j];
		newoption.innerHTML+=' ('+bindingdataunits[j]+')';
		newdatatypeselect.appendChild(newoption);
	}	
	newinputline.appendChild(newdatatypeselect);

	var newinputvalue = document.createElement('input');
	newinputvalue.setAttribute('type','text');
	newinputvalue.setAttribute('name','bindingdata_value_new_'+i.toString());
	newinputvalue.setAttribute('id','bindingdata_value_new_'+i.toString());
	newinputvalue.setAttribute('size','5');	
	newinputline.appendChild(newinputvalue);

	var newnoteslink = document.createElement('a');
	newnoteslink.setAttribute('href','#');
	var newnotesicon = document.createElement('img');
	newnotesicon.setAttribute('src','notes_icon.png');
	newnotesicon.setAttribute('height','20');
	newnotesicon.setAttribute('onclick','popnotes(\'bindingdata_notes_new_'+i.toString()+'\');');
	newnoteslink.appendChild(newnotesicon);	
	newinputline.appendChild(newnoteslink);

	var newnotesdiv = document.createElement('div');
	newnotesdiv.setAttribute('class','div_notespopup');
	newnotesdiv.setAttribute('id','bindingdata_notes_new_'+i.toString());
	newnotesdiv.innerHTML='<br />Notes about this data:<br />';
	newnotesdiv.innerHTML+='<textarea class="textarea_notespopup" id="textarea_bindingdata_notes_new_'+i.toString()+'" name="textarea_bindingdata_notes_new_'+i.toString()+'">';
	newnotesdiv.innerHTML+='</textarea>';
	newnotesdiv.innerHTML+='<span class="span_closenotespopup" onclick="closenotes(\'bindingdata_notes_new_'+i.toString()+'\');"><a href="#">X</a></span>';	
	newinputline.appendChild(newnotesdiv);		

	t.appendChild(newinputline);

	if(num_bindingdata>4){
		var t = document.getElementById("button_morebindingdata");
		t.style.display='none';
	}
	if(num_bindingdata>1 && num_bindingdata<5){
		var t = document.getElementById("button_morebindingdata");
		t.style.display='inline';
	}	
	if(num_bindingdata>0){
		var t = document.getElementById("button_lessbindingdata");
		if(t){
			t.style.display='inline';
		}
	}
}
function populatebindingdata(dataid,datatype,targetid,value,commentid,comment){
	var t = document.getElementById("bindingdatainputlines");
	var i = dataid;
	
	document.getElementById("input_oldbindingdataids").value+=dataid+',';
	document.getElementById("input_oldcommentids").value+=commentid+',';	

	var newinputline = document.createElement('div');		
	newinputline.setAttribute('id','div_bindingdata_'+i.toString());	
	newinputline.setAttribute('class','nonlinks');	
	newinputline.setAttribute('style','text-align:right;');

	var newtargetselect = document.createElement('select');
	newtargetselect.setAttribute('id','bindingdata_targetid_'+i.toString());
	newtargetselect.setAttribute('name','bindingdata_targetid_'+i.toString());
	for(var j=0;j<targetnames.length;j++){
		var newoption = document.createElement('option');
		newoption.setAttribute('value',targetids[j]);
		if(targetids[j]==targetid){
			newoption.setAttribute('selected','selected');
		}
		newoption.innerHTML=targetnames[j];
		newtargetselect.appendChild(newoption);
	}	
	newinputline.appendChild(newtargetselect);

	var newdatatypeselect = document.createElement('select');
	newdatatypeselect.setAttribute('id','bindingdata_datatypeid_'+i.toString());
	newdatatypeselect.setAttribute('name','bindingdata_datatypeid_'+i.toString());
	for(var j=0;j<bindingdatatypes.length;j++){
		var newoption = document.createElement('option');
		newoption.setAttribute('value',bindingdataids[j]);
		if(bindingdataids[j]==datatype){
			newoption.setAttribute('selected','selected');
		}
		newoption.innerHTML=bindingdatatypes[j];
		newoption.innerHTML+=' ('+newoption.innerHTML+') ';
		newdatatypeselect.appendChild(newoption);
	}	
	newinputline.appendChild(newdatatypeselect);

	var newinputvalue = document.createElement('input');
	newinputvalue.setAttribute('type','text');
	newinputvalue.setAttribute('name','bindingdata_value_'+i.toString());
	newinputvalue.setAttribute('size','5');
	newinputvalue.setAttribute('value',value);
	newinputline.appendChild(newinputvalue);

	var newnoteslink = document.createElement('a');
	newnoteslink.setAttribute('href','#');
	var newnotesicon = document.createElement('img');
	newnotesicon.setAttribute('src','notes_icon.png');
	newnotesicon.setAttribute('height','20');
	if(comment.length==0) newnotesicon.setAttribute('style','opacity:0.45');
	newnotesicon.setAttribute('onclick','popnotes(\'bindingdata_notes_'+i.toString()+'\');');
	newnoteslink.appendChild(newnotesicon);	
	newinputline.appendChild(newnoteslink);

	var deletelink = document.createElement('a');
	deletelink.setAttribute('href','#');
	var deleteicon = document.createElement('img');
	deleteicon.setAttribute('src','delete_icon.png');
	deleteicon.setAttribute('height','20');
	deleteicon.setAttribute('style','margin-left:18px;');
	deleteicon.setAttribute('onclick','deletecheck(\''+i.toString()+'\');');
	deletelink.appendChild(deleteicon);	
	newinputline.appendChild(deletelink);

	var newnotesdiv = document.createElement('div');
	newnotesdiv.setAttribute('class','div_notespopup');
	newnotesdiv.setAttribute('id','bindingdata_notes_'+i.toString());
	newnotesdiv.innerHTML='<br />Notes about this data:<br />';
	newnotesdiv.innerHTML+='<textarea class="textarea_notespopup" id="textarea_bindingdata_notes_'+i.toString()+'" name="textarea_bindingdata_notes_'+i.toString()+'">'+comment+'</textarea>';
	newnotesdiv.innerHTML+='<input type="hidden" name="bindingdata_notesid_'+i.toString()+'" id="bindingdata_notesid_'+i.toString()+'" value="'+commentid.toString()+'" />';
	newnotesdiv.innerHTML+='<span class="span_closenotespopup" onclick="closenotes(\'bindingdata_notes_'+i.toString()+'\');"><a href="#">X</a></span>';	
	newinputline.appendChild(newnotesdiv);		

	t.appendChild(newinputline);

	if(num_bindingdata>4){
		var t = document.getElementById("button_morebindingdata");
		t.style.display='none';
	}
	if(num_bindingdata>1 && num_bindingdata<5){
		var t = document.getElementById("button_morebindingdata");
		t.style.display='inline';
	}	
	if(num_bindingdata>0){
		var t = document.getElementById("button_lessbindingdata");
		if(t){
			t.style.display='inline';
		}
	}
}

function lessbindingdata(){
	var t = document.getElementById("bindingdatainputlines");
	var l = t.getElementsByTagName('div');	
	var lindex=l.length-1;
	l[lindex].parentNode.removeChild(l[lindex]);
	l[lindex-1].parentNode.removeChild(l[lindex-1]);
	num_bindingdata-=1;
	if(num_bindingdata==0){
		t = document.getElementById("button_lessbindingdata");
		t.style.display='none';
	}
	if(num_bindingdata<5){
		t = document.getElementById("button_morebindingdata");
		t.style.display='inline';
	}
}

function morepropertydata(){
	var t = document.getElementById("propertydatainputlines");
	num_propertydata+=1;
	var i = num_propertydata;
	
	var newinputline = document.createElement('div');		
	newinputline.setAttribute('id','div_propertydata_new_'+i.toString());	
	newinputline.setAttribute('class','nonlinks');	

	var newdatatypeselect = document.createElement('select');
	newdatatypeselect.setAttribute('id','propertydata_datatypeid_new_'+i.toString());
	newdatatypeselect.setAttribute('name','propertydata_datatypeid_new_'+i.toString());
	for(var j=0;j<propertydatatypes.length;j++){
		var newoption = document.createElement('option');
		newoption.setAttribute('value',propertydataids[j]);
		newoption.innerHTML=propertydatatypes[j];
		newoption.innerHTML+=' ('+propertydataunits[j]+')';
		newdatatypeselect.appendChild(newoption);
	}	
	newinputline.appendChild(newdatatypeselect);

	var newinputvalue = document.createElement('input');
	newinputvalue.setAttribute('type','text');
	newinputvalue.setAttribute('name','propertydata_value_new_'+i.toString());
	newinputvalue.setAttribute('size','5');	
	newinputline.appendChild(newinputvalue);

	var newnoteslink = document.createElement('a');
	newnoteslink.setAttribute('href','#');
	var newnotesicon = document.createElement('img');
	newnotesicon.setAttribute('src','notes_icon.png');
	newnotesicon.setAttribute('height','20');
	newnotesicon.setAttribute('onclick','popnotes(\'propertydata_notes_new_'+i.toString()+'\');');
	newnoteslink.appendChild(newnotesicon);	
	newinputline.appendChild(newnoteslink);

	var newnotesdiv = document.createElement('div');
	newnotesdiv.setAttribute('class','div_notespopup');
	newnotesdiv.setAttribute('id','propertydata_notes_new_'+i.toString());
	newnotesdiv.innerHTML='<br />Notes about this data:<br />';
	newnotesdiv.innerHTML+='<textarea class="textarea_notespopup" id="textarea_propertydata_notes_new_'+i.toString()+'" name="textarea_propertydata_notes_new_'+i.toString()+'">';
	newnotesdiv.innerHTML+='</textarea>';
	newnotesdiv.innerHTML+='<span class="span_closenotespopup" onclick="closenotes(\'propertydata_notes_new_'+i.toString()+'\');"><a href="#">X</a></span>';	
	newinputline.appendChild(newnotesdiv);		

	t.appendChild(newinputline);

	if(num_propertydata>4){
		var t = document.getElementById("button_morepropertydata");
		t.style.display='none';
	}
	if(num_propertydata>1 && num_propertydata<5){
		var t = document.getElementById("button_morepropertydata");
		t.style.display='inline';
	}	
	if(num_propertydata>0){
		var t = document.getElementById("button_lesspropertydata");
		if(t){
			t.style.display='inline';
		}
	}
}
function populatepropertydata(dataid,datatype,value,commentid,comment){
	var t = document.getElementById("propertydatainputlines");
	var i = dataid;
	
	document.getElementById("input_oldpropertydataids").value+=dataid+',';
	document.getElementById("input_oldcommentids").value+=commentid+',';	

	var newinputline = document.createElement('div');		
	newinputline.setAttribute('id','div_propertydata_'+i.toString());	
	newinputline.setAttribute('class','nonlinks');	

	var newdatatypeselect = document.createElement('select');
	newdatatypeselect.setAttribute('id','propertydata_datatypeid_'+i.toString());
	newdatatypeselect.setAttribute('name','propertydata_datatypeid_'+i.toString());
	for(var j=0;j<propertydatatypes.length;j++){
		var newoption = document.createElement('option');
		newoption.setAttribute('value',propertydataids[j]);
		if(propertydataids[j]==datatype){
			newoption.setAttribute('selected','selected');
		}
		newoption.innerHTML=propertydatatypes[j];
		newoption.innerHTML+=' ('+propertydataunits[j]+')';
		newdatatypeselect.appendChild(newoption);
	}	
	newinputline.appendChild(newdatatypeselect);

	var newinputvalue = document.createElement('input');
	newinputvalue.setAttribute('type','text');
	newinputvalue.setAttribute('name','propertydata_value_'+i.toString());
	newinputvalue.setAttribute('size','5');	
	newinputvalue.setAttribute('value',value);
	newinputline.appendChild(newinputvalue);

	var newnoteslink = document.createElement('a');
	newnoteslink.setAttribute('href','#');
	var newnotesicon = document.createElement('img');
	newnotesicon.setAttribute('src','notes_icon.png');
	newnotesicon.setAttribute('height','20');
	if(comment.length<1){
		 newnotesicon.setAttribute('style','opacity:.3');
	}
	newnotesicon.setAttribute('onclick','popnotes(\'propertydata_notes_'+i.toString()+'\');');
	newnoteslink.appendChild(newnotesicon);	
	newinputline.appendChild(newnoteslink);

	var newnotesdiv = document.createElement('div');
	newnotesdiv.setAttribute('class','div_notespopup');
	newnotesdiv.setAttribute('id','propertydata_notes_'+i.toString());
	newnotesdiv.innerHTML='<br />Notes about this data:<br />';
	newnotesdiv.innerHTML+='<textarea class="textarea_notespopup" name="textarea_propertydata_notes_'+i.toString()+'">'+comment+'</textarea>';
	newnotesdiv.innerHTML+='<input type="hidden" name="propertydata_notesid_'+i.toString()+'" id="propertydata_notesid_'+i.toString()+'" value="'+commentid.toString()+'" />';
	newnotesdiv.innerHTML+='<span class="span_closenotespopup" onclick="closenotes(\'propertydata_notes_'+i.toString()+'\');"><a href="#">X</a></span>';	
	newinputline.appendChild(newnotesdiv);		

	var deletelink = document.createElement('a');
	deletelink.setAttribute('href','#');
	var deleteicon = document.createElement('img');
	deleteicon.setAttribute('src','delete_icon.png');
	deleteicon.setAttribute('height','20');
	deleteicon.setAttribute('style','margin-left:18px;');
	deleteicon.setAttribute('onclick','deletecheck(\''+i.toString()+'\');');
	deletelink.appendChild(deleteicon);	
	newinputline.appendChild(deletelink);

	t.appendChild(newinputline);

	if(num_propertydata>4){
		var t = document.getElementById("button_morepropertydata");
		t.style.display='none';
	}
	if(num_propertydata>1 && num_propertydata<5){
		var t = document.getElementById("button_morepropertydata");
		t.style.display='inline';
	}	
	if(num_propertydata>0){
		var t = document.getElementById("button_lesspropertydata");
		if(t){
			t.style.display='inline';
		}
	}
}
function lesspropertydata(){
	var t = document.getElementById("propertydatainputlines");
	var l = t.getElementsByTagName('div');	
	var lindex=l.length-1;
	l[lindex].parentNode.removeChild(l[lindex]);
	l[lindex-1].parentNode.removeChild(l[lindex-1]);
	num_propertydata-=1;
	if(num_propertydata==0){
		t = document.getElementById("button_lesspropertydata");
		t.style.display='none';
	}
	if(num_propertydata<5){
		t = document.getElementById("button_morepropertydata");
		t.style.display='inline';
	}
}
function populatedocdata(filename,dataid,datatype,commentid,comment){
	var t = document.getElementById("docdatainputlines");
	var i = dataid;
	
	document.getElementById("input_olddocdataids").value+=dataid+',';
	document.getElementById("input_oldcommentids").value+=commentid+',';	

	var newinputline = document.createElement('div');		
	newinputline.setAttribute('id','div_docdata_'+i.toString());	
	//newinputline.setAttribute('class','nonlinks');	

	var hiddendatatype = document.createElement('input');
	hiddendatatype.setAttribute('type','hidden');
	hiddendatatype.setAttribute('name','docdata_datatypeid_'+i.toString());
	hiddendatatype.setAttribute('value',datatype.toString());
	newinputline.appendChild(hiddendatatype);

	hiddendatatype = document.createElement('input');
	hiddendatatype.setAttribute('type','hidden');
	hiddendatatype.setAttribute('name','docdata_filename_'+i.toString());
	hiddendatatype.setAttribute('value',filename);
	newinputline.appendChild(hiddendatatype);

	var doclink = document.createElement('a');
	doclink.setAttribute('href',filename);
	doclink.setAttribute('title',filename.replace('uploads/documents/',''));
	doclink.innerHTML=docdatatypes[docdataids.indexOf(datatype.toString())]+' ';
	newinputline.appendChild(doclink);

	var newnoteslink = document.createElement('a');
	newnoteslink.setAttribute('href','#');
	var newnotesicon = document.createElement('img');
	newnotesicon.setAttribute('src','notes_icon.png');
	newnotesicon.setAttribute('height','20');
	if(comment.length<1){
		 newnotesicon.setAttribute('style','opacity:.3');
	}
	newnotesicon.setAttribute('onclick','popnotes(\'docdata_notes_'+i.toString()+'\');');
	newnoteslink.appendChild(newnotesicon);	
	newinputline.appendChild(newnoteslink);

	var newnotesdiv = document.createElement('div');
	newnotesdiv.setAttribute('class','div_notespopup');
	newnotesdiv.setAttribute('id','docdata_notes_'+i.toString());
	newnotesdiv.innerHTML='<br />Notes about this data:<br />';
	newnotesdiv.innerHTML+='<a href="'+filename+'">'+filename.replace('uploads/documents/','')+'</a><br />';
	newnotesdiv.innerHTML+='<textarea class="textarea_notespopup" name="textarea_docdata_notes_'+i.toString()+'">'+comment+'</textarea>';
	newnotesdiv.innerHTML+='<input type="hidden" name="docdata_notesid_'+i.toString()+'" id="docdata_notesid_'+i.toString()+'" value="'+commentid.toString()+'" />';
	newnotesdiv.innerHTML+='<span class="span_closenotespopup" onclick="closenotes(\'docdata_notes_'+i.toString()+'\');"><a href="#">X</a></span>';	
	newinputline.appendChild(newnotesdiv);		

	var deletelink = document.createElement('a');
	deletelink.setAttribute('href','#');
	var deleteicon = document.createElement('img');
	deleteicon.setAttribute('src','delete_icon.png');
	deleteicon.setAttribute('height','20');
	deleteicon.setAttribute('style','margin-left:18px;');
	deleteicon.setAttribute('onclick','deletedatacheck(\''+i.toString()+'\',\''+datatype.toString()+'\');');
	deletelink.appendChild(deleteicon);	
	newinputline.appendChild(deletelink);

	t.appendChild(newinputline);

	if(num_docdata>4){
		var t = document.getElementById("button_moredocdata");
		t.style.display='none';
	}
	if(num_docdata>1 && num_docdata<5){
		var t = document.getElementById("button_moredocdata");
		t.style.display='inline';
	}	
	if(num_docdata>0){
		var t = document.getElementById("button_lessdocdata");
		if(t){
			t.style.display='inline';
		}
	}
}

function moredocdata(){
	var t = document.getElementById("docdatainputlines");
	num_docdata+=1;
	var i = num_docdata;
	
	var newinputline = document.createElement('div');		
	newinputline.setAttribute('id','div_docdata_new_'+i.toString());	
	newinputline.setAttribute('class','nonlinks');	

	var newdatatypeselect = document.createElement('select');
	newdatatypeselect.setAttribute('id','docdata_datatypeid_new_'+i.toString());
	newdatatypeselect.setAttribute('name','docdata_datatypeid_new_'+i.toString());
	for(var j=0;j<docdatatypes.length;j++){
		var newoption = document.createElement('option');
		newoption.setAttribute('value',docdataids[j]);
		newoption.innerHTML=docdatatypes[j];
		newdatatypeselect.appendChild(newoption);
	}	
	newinputline.appendChild(newdatatypeselect);

	var newinputvalue = document.createElement('input');
	newinputvalue.setAttribute('type','file');
	newinputvalue.setAttribute('name','docdata_value_new_'+i.toString());
	newinputvalue.setAttribute('accept','.pdf,.doc,.fid,application/pdf,application/msword,.png,.sdf,.mol');
	newinputvalue.setAttribute('style','width:190px;font-size:0.8em;');	
	newinputline.appendChild(newinputvalue);

	var newnoteslink = document.createElement('a');
	newnoteslink.setAttribute('href','#');
	var newnotesicon = document.createElement('img');
	newnotesicon.setAttribute('src','notes_icon.png');
	newnotesicon.setAttribute('height','20');
	newnotesicon.setAttribute('onclick','popnotes(\'docdata_notes_new_'+i.toString()+'\');');
	newnoteslink.appendChild(newnotesicon);	
	newinputline.appendChild(newnoteslink);

	var newnotesdiv = document.createElement('div');
	newnotesdiv.setAttribute('class','div_notespopup');
	newnotesdiv.setAttribute('id','docdata_notes_new_'+i.toString());
	newnotesdiv.innerHTML='<br />Notes about this File:<br />';
	newnotesdiv.innerHTML+='<textarea class="textarea_notespopup" id="textarea_docdata_notes_new_'+i.toString()+'" name="textarea_docdata_notes_new_'+i.toString()+'">';
	newnotesdiv.innerHTML+='</textarea>';
	newnotesdiv.innerHTML+='<span class="span_closenotespopup" onclick="closenotes(\'docdata_notes_new_'+i.toString()+'\');"><a href="#">X</a></span>';	
	newinputline.appendChild(newnotesdiv);		

	t.appendChild(newinputline);

	if(num_docdata>4){
		var t = document.getElementById("button_moredocdata");
		t.style.display='none';
	}
	if(num_docdata>1 && num_docdata<5){
		var t = document.getElementById("button_moredocdata");
		t.style.display='inline';
	}	
	if(num_docdata>0){
		var t = document.getElementById("button_lessdocdata");
		if(t){
			t.style.display='inline';
		}
	}
}
function lessdocdata(){
	var t = document.getElementById("docdatainputlines");
	var l = t.getElementsByTagName('div');	
	var lindex=l.length-1;
	l[lindex].parentNode.removeChild(l[lindex]);
	l[lindex-1].parentNode.removeChild(l[lindex-1]);
	num_docdata-=1;
	if(num_docdata==0){
		t = document.getElementById("button_lessdocdata");
		t.style.display='none';
	}
	if(num_docdata<5){
		t = document.getElementById("button_moredocdata");
		t.style.display='inline';
	}
}
function deletecheck(dataid){
	var t = document.getElementById("deletedataid");
	t.value = dataid.toString();
	t = document.getElementById("div_deletecheck");
	t.style.display='block';
}
function deletedatacheck(dataid,datatype){
	var t = document.getElementById("deletedataid");
	t.value = dataid.toString();
	var t = document.getElementById("deletedocdatatype");
	t.value = datatype.toString();
	t = document.getElementById("div_deletecheck");
	t.style.display='block';
}
function closedeletecheck(){
	var t = document.getElementById("deletedataid");
	t.value = '';
	t = document.getElementById("div_deletecheck");
	t.style.display='none';
}
var bindingdatatypes = new Array('IC50','EC50','kd');
var bindingdataids = new Array('1','2','3');
var bindingdataunits = new Array('&mu;M','&mu;M','&mu;M');
var propertydatatypes = new Array('CC50','Aq. Solubility');
var propertydataids = new Array('4','5');
var propertydataunits = new Array('&mu;M','g/mL');
var docdatatypes = new Array('H NMR','C NMR','Mass Spec.','Synthesis','Manuscript','Structure','Image','Other');
var docdataids = new Array('6','7','8','9','10','11','13','15');



