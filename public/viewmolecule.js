function switchdatadiv(selected){
	var t = document.getElementById("div_tabbindingdata");
	t.className="datatab";
	t = document.getElementById("div_tabpropertydata");
	t.className="datatab";
	t = document.getElementById("div_tabdocdata");
	t.className="datatab";
	t = document.getElementById("div_tabmodelingdata");
	t.className="datatab";
	t = document.getElementById("div_tabcommentdata");
	t.className="datatab";
	t = document.getElementById("div_tab"+selected);
	t.className="datatab datatabopen";

	t = document.getElementById("div_bindingdata");
	t.style.display="none";
	t = document.getElementById("div_propertydata");
	t.style.display="none";
	t = document.getElementById("div_docdata");
	t.style.display="none";
	t = document.getElementById("div_modelingdata");
	t.style.display="none";
	t = document.getElementById("div_commentdata");
	t.style.display="none";
	t = document.getElementById("div_"+selected);
	t.style.display="block";
}
function deletecheck(){
    var t = document.getElementById("div_deletecheck");
    t.style.display = 'block';
}
function closedeletecheck(){
     var t = document.getElementById("div_deletecheck");
    t.style.display = 'none';
}
