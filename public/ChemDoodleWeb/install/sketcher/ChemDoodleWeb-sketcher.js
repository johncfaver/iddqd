//
// ChemDoodle Web Components 4.7.0
//
// http://web.chemdoodle.com
//
// Copyright 2009 iChemLabs, LLC.  All rights reserved.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// As a special exception to the GPL, any HTML file in a public website
// or any free web service which merely makes function calls to this
// code, and for that purpose includes it by reference, shall be deemed
// a separate work for copyright law purposes. If you modify this code,
// you may extend this exception to your version of the code, but you
// are not obligated to do so. If you do not wish to do so, delete this
// exception statement from your version.
//
// As an additional exception to the GPL, you may distribute this
// packed form of the code without the copy of the GPL license normally
// required, provided you include this license notice and a URL through
// which recipients can access the corresponding unpacked source code.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// Please contact iChemLabs <http://www.ichemlabs.com/contact> for
// alternate licensing options.
//
ChemDoodle.sketcher=function(){
	var a={actions:{},gui:{}};
	a.gui.desktop={};
	a.gui.mobile={};
	a.states={};
	return a
}();

var cgidir='../../../cgi-bin/';

(function(a){a._Action=function(){this.forward=function(a){this.innerForward();
this.checks(a)};
this.reverse=function(a){this.innerReverse();
this.checks(a)};
this.checks=function(a){a.molecule.check();
a.repaint()};
return!0}})(ChemDoodle.sketcher.actions);



(function(a,e){	
	a.AddAction=function(a,b,f){
		this.mol=a;
		this.as=b;
		this.bs=f;
		return!0
	};
	a.AddAction.prototype=new a._Action;
	a.AddAction.prototype.innerForward=function(){
		if(null!=this.as)for(var a=0,b=this.as.length;a<b;a++)this.mol.atoms.push(this.as[a]);
		if(null!=this.bs){a=0;for(b=this.bs.length;a<b;a++)this.mol.bonds.push(this.bs[a])}
	};
	a.AddAction.prototype.innerReverse=function(){
		if(null!=this.as){
			for(var a=[],b=0,f=this.mol.atoms.length;b<f;b++)-1==e(this.mol.atoms[b],this.as)&&a.push(this.mol.atoms[b]);
			this.mol.atoms=a}
		if(null!=this.bs){
			a=[];
			b=0;
			for(f=this.mol.bonds.length;b<f;b++)-1==e(this.mol.bonds[b],this.bs)&&a.push(this.mol.bonds[b]);
			this.mol.bonds=a
		}
	}
})(ChemDoodle.sketcher.actions,jQuery.inArray);

(function(a,e){a.ChangeBondAction=function(a,b,f){this.b=a;
this.orderBefore=a.bondOrder;
this.stereoBefore=a.stereo;
b?(this.orderAfter=b,this.stereoAfter=f):(this.orderAfter=a.bondOrder+1,3<this.orderAfter&&(this.orderAfter=1),this.stereoAfter=e.STEREO_NONE);
return!0};
a.ChangeBondAction.prototype=new a._Action;
a.ChangeBondAction.prototype.innerForward=function(){this.b.bondOrder=this.orderAfter;
this.b.stereo=this.stereoAfter};
a.ChangeBondAction.prototype.innerReverse=function(){this.b.bondOrder=this.orderBefore;

this.b.stereo=this.stereoBefore}})(ChemDoodle.sketcher.actions,ChemDoodle.structures.Bond);
(function(a){a.ChangeChargeAction=function(a,g){this.a=a;
this.delta=g;
return!0};
a.ChangeChargeAction.prototype=new a._Action;
a.ChangeChargeAction.prototype.innerForward=function(){this.a.charge+=this.delta};
a.ChangeChargeAction.prototype.innerReverse=function(){this.a.charge-=this.delta}})(ChemDoodle.sketcher.actions);

(function(a){a.ChangeCoordinatesAction=function(a,g){this.as=a;
this.recs=[];
for(var b=0,f=this.as.length;
b<f;
b++)this.recs[b]={xo:this.as[b].x,yo:this.as[b].y,xn:g[b].x,yn:g[b].y};
return!0};
a.ChangeCoordinatesAction.prototype=new a._Action;
a.ChangeCoordinatesAction.prototype.innerForward=function(){for(var a=0,g=this.as.length;
a<g;
a++)this.as[a].x=this.recs[a].xn,this.as[a].y=this.recs[a].yn};
a.ChangeCoordinatesAction.prototype.innerReverse=function(){for(var a=0,g=this.as.length;
a<g;
a++)this.as[a].x=
this.recs[a].xo,this.as[a].y=this.recs[a].yo}})(ChemDoodle.sketcher.actions);
(function(a){a.ChangeLabelAction=function(a,g){this.a=a;
this.before=a.label;
this.after=g;
return!0};
a.ChangeLabelAction.prototype=new a._Action;
a.ChangeLabelAction.prototype.innerForward=function(){this.a.label=this.after};
a.ChangeLabelAction.prototype.innerReverse=function(){this.a.label=this.before}})(ChemDoodle.sketcher.actions);

(function(a){a.ChangeLonePairAction=function(a,g){this.a=a;
this.delta=g;
return!0};
a.ChangeLonePairAction.prototype=new a._Action;
a.ChangeLonePairAction.prototype.innerForward=function(){this.a.numLonePair+=this.delta};
a.ChangeLonePairAction.prototype.innerReverse=function(){this.a.numLonePair-=this.delta}})(ChemDoodle.sketcher.actions);

(function(a){a.ClearAction=function(a){this.sketcher=a;
this.before=this.sketcher.getMolecule();
this.sketcher.clear();
this.after=this.sketcher.getMolecule();
return!0};
a.ClearAction.prototype=new a._Action;
a.ClearAction.prototype.innerForward=function(){this.sketcher.molecule=this.after};
a.ClearAction.prototype.innerReverse=function(){this.sketcher.molecule=this.before}})(ChemDoodle.sketcher.actions);

(function(a){a.DeleteAction=function(a,g,b){this.mol=a;
this.as=g;
this.bs=b;
return!0};
a.DeleteAction.prototype=new a._Action;
a.DeleteAction.prototype.innerForward=a.AddAction.prototype.innerReverse;
a.DeleteAction.prototype.innerReverse=a.AddAction.prototype.innerForward})(ChemDoodle.sketcher.actions);

(function(a){a.FlipBondAction=function(a){this.b=a;
return!0};
a.FlipBondAction.prototype=new a._Action;
a.FlipBondAction.prototype.innerForward=function(){var a=this.b.a1;
this.b.a1=this.b.a2;
this.b.a2=a};
a.FlipBondAction.prototype.innerReverse=function(){this.innerForward()}})(ChemDoodle.sketcher.actions);

(function(a){a.MoveAction=function(a,g){this.as=a;
this.dif=g;
return!0};
a.MoveAction.prototype=new a._Action;
a.MoveAction.prototype.innerForward=function(){for(var a=0,g=this.as.length;
a<g;
a++)this.as[a].add(this.dif)};
a.MoveAction.prototype.innerReverse=function(){for(var a=0,g=this.as.length;
a<g;
a++)this.as[a].sub(this.dif)}})(ChemDoodle.sketcher.actions);

(function(a,e){a.RotateAction=function(a,b,f){this.as=a;
this.dif=b;
this.center=f;
return!0};
a.RotateAction.prototype=new a._Action;
a.RotateAction.prototype.innerForward=function(){for(var a=0,b=this.as.length;
a<b;
a++){var f=this.center.distance(this.as[a]),d=this.center.angle(this.as[a])+this.dif;
this.as[a].x=this.center.x+f*e.cos(d);
this.as[a].y=this.center.y-f*e.sin(d)}};
a.RotateAction.prototype.innerReverse=function(){for(var a=0,b=this.as.length;
a<b;
a++){var f=this.center.distance(this.as[a]),
d=this.center.angle(this.as[a])-this.dif;
this.as[a].x=this.center.x+f*e.cos(d);
this.as[a].y=this.center.y-f*e.sin(d)}}})(ChemDoodle.sketcher.actions,Math);

(function(a){a.SwitchMoleculeAction=function(a,g){this.sketcher=a;
this.molB=a.molecule;
this.molA=g;
return!0};
a.SwitchMoleculeAction.prototype=new a._Action;
a.SwitchMoleculeAction.prototype.innerForward=function(){this.sketcher.loadMolecule(this.molA)};
a.SwitchMoleculeAction.prototype.innerReverse=function(){this.sketcher.molecule=this.molB}})(ChemDoodle.sketcher.actions,jQuery.inArray);

(function(a){a.HistoryManager=function(a){this.sketcher=a;
this.undoStack=[];
this.redoStack=[];
return!0};
a.HistoryManager.prototype.undo=function(){if(0!=this.undoStack.length){var a=this.undoStack.pop();
a.reverse(this.sketcher);
this.redoStack.push(a);
0==this.undoStack.length&&this.sketcher.toolbarManager.buttonUndo.disable();
this.sketcher.toolbarManager.buttonRedo.enable()}};
a.HistoryManager.prototype.redo=function(){if(0!=this.redoStack.length){var a=this.redoStack.pop();
a.forward(this.sketcher);

this.undoStack.push(a);
this.sketcher.toolbarManager.buttonUndo.enable();
0==this.redoStack.length&&this.sketcher.toolbarManager.buttonRedo.disable()}};
a.HistoryManager.prototype.pushUndo=function(a){a.forward(this.sketcher);
this.undoStack.push(a);
0!=this.redoStack.length&&(this.redoStack=[]);
this.sketcher.toolbarManager.buttonUndo.enable();
this.sketcher.toolbarManager.buttonRedo.disable()};
a.HistoryManager.prototype.clear=function(){0!=this.undoStack.length&&(this.undoStack=[],this.sketcher.toolbarManager.buttonUndo.disable());

0!=this.redoStack.length&&(this.redoStack=[],this.sketcher.toolbarManager.buttonRedo.disable())}})(ChemDoodle.sketcher.actions);

(function(a,e,g,b,f,d,m,j){b._State=function(){return!0};
b._State.prototype.setup=function(a){this.sketcher=a};
b._State.prototype.bondExists=function(a,c){for(var j=0,b=this.sketcher.molecule.bonds.length;
j<b;
j++)if(this.sketcher.molecule.bonds[j].contains(a)&&this.sketcher.molecule.bonds[j].contains(c))return!0;
return!1};
b._State.prototype.getBond=function(a,c){for(var j=0,b=this.sketcher.molecule.bonds.length;
j<b;
j++)if(this.sketcher.molecule.bonds[j].contains(a)&&this.sketcher.molecule.bonds[j].contains(c))return this.sketcher.molecule.bonds[j];

return null};
b._State.prototype.clearHover=function(){null!=this.sketcher.hovering&&(this.sketcher.hovering.isHover=!1,this.sketcher.hovering.isSelected=!1);
this.sketcher.hovering=null};
b._State.prototype.findHoveredObject=function(a,c,j){this.clearHover();
var b=Infinity,f=null;
if(c)for(var c=0,d=this.sketcher.molecule.atoms.length;
c<d;
c++){this.sketcher.molecule.atoms[c].isHover=!1;
var g=a.p.distance(this.sketcher.molecule.atoms[c]);
g<this.sketcher.specs.bondLength&&g<b&&(b=g,f=this.sketcher.molecule.atoms[c])}if(j){c=
0;
for(d=this.sketcher.molecule.bonds.length;
c<d;
c++)this.sketcher.molecule.bonds[c].isHover=!1,g=a.p.distance(this.sketcher.molecule.bonds[c].getCenter()),g<this.sketcher.specs.bondLength&&g<b&&(b=g,f=this.sketcher.molecule.bonds[c])}null!=f&&(f.isHover=!0,this.sketcher.hovering=f)};
b._State.prototype.getOptimumAngle=function(b){var b=this.sketcher.molecule.getAngles(b),c=0;
if(0==b.length)c=j.PI/6;
else if(1==b.length){for(var c=null,f=0,d=this.sketcher.molecule.bonds.length;
f<d;
f++)this.sketcher.molecule.bonds[f].contains(this.sketcher.hovering)&&
(c=this.sketcher.molecule.bonds[f]);
3<=c.bondOrder?c=b[0]+j.PI:(c=2*(b[0]%j.PI),c=a.isBetween(c,0,j.PI/2)||a.isBetween(c,j.PI,3*j.PI/2)?b[0]+2*j.PI/3:b[0]-2*j.PI/3)}else c=a.angleBetweenLargest(b).angle;
return c};
b._State.prototype.click=function(a){this.innerclick&&this.innerclick(a)};
b._State.prototype.rightclick=function(a){this.innerrightclick&&this.innerrightclick(a)};
b._State.prototype.dblclick=function(a){this.innerdblclick&&this.innerdblclick(a);
null==this.sketcher.hovering&&(a=new f.Point(this.sketcher.width/
2,this.sketcher.height/2),a.sub(this.sketcher.molecule.getCenter()),this.sketcher.historyManager.pushUndo(new g.MoveAction(this.sketcher.molecule.atoms,a)))};
b._State.prototype.mousedown=function(a){this.sketcher.lastPoint=a.p;
this.sketcher.isHelp||this.sketcher.isMobile&&10>a.op.distance(this.sketcher.helpPos)?(this.sketcher.isHelp=!1,this.sketcher.repaint(),window.open("http://web.chemdoodle.com/sketcher")):this.innermousedown&&this.innermousedown(a)};
b._State.prototype.rightmousedown=function(a){this.innerrightmousedown&&
this.innerrightmousedown(a)};
b._State.prototype.mousemove=function(a){this.innermousemove&&this.innermousemove(a);
this.sketcher.repaint()};
b._State.prototype.mouseout=function(a){this.innermouseout&&this.innermouseout(a)};
b._State.prototype.mouseover=function(a){this.innermouseover&&this.innermouseover(a)};
b._State.prototype.mouseup=function(a){this.parentAction=null;
this.innermouseup&&this.innermouseup(a)};
b._State.prototype.rightmouseup=function(a){this.innerrightmouseup&&this.innerrightmouseup(a)};

b._State.prototype.mousewheel=function(a,c){this.innermousewheel&&this.innermousewheel(a);
this.sketcher.specs.scale+=c/10;
this.sketcher.checkScale();
this.sketcher.repaint()};
b._State.prototype.drag=function(a){this.innerdrag&&this.innerdrag(a);
if(null==this.sketcher.hovering){if(e.SHIFT){var c=new f.Point(this.sketcher.width/2,this.sketcher.height/2),b=c.angle(this.sketcher.lastPoint),d=c.angle(a.p)-b;
if(null==this.parentAction)this.parentAction=new g.RotateAction(this.sketcher.molecule.atoms,d,c),
this.sketcher.historyManager.pushUndo(this.parentAction);
else{this.parentAction.dif+=d;
for(var b=0,i=this.sketcher.molecule.atoms.length;
b<i;
b++){var h=c.distance(this.sketcher.molecule.atoms[b]),m=c.angle(this.sketcher.molecule.atoms[b])+d;
this.sketcher.molecule.atoms[b].x=c.x+h*j.cos(m);
this.sketcher.molecule.atoms[b].y=c.y-h*j.sin(m)}this.sketcher.molecule.check()}}else{if(!this.sketcher.lastPoint)return;
c=new f.Point(a.p.x,a.p.y);
c.sub(this.sketcher.lastPoint);
if(null==this.parentAction)this.parentAction=
new g.MoveAction(this.sketcher.molecule.atoms,c),this.sketcher.historyManager.pushUndo(this.parentAction);
else{this.parentAction.dif.add(c);
b=0;
for(i=this.sketcher.molecule.atoms.length;
b<i;
b++)this.sketcher.molecule.atoms[b].add(c);
this.sketcher.molecule.check()}}this.sketcher.repaint()}this.sketcher.lastPoint=a.p};
b._State.prototype.keydown=function(b){if(e.CANVAS_DRAGGING==this.sketcher)null!=this.sketcher.lastPoint&&(b.p=this.sketcher.lastPoint,this.drag(b));
else if(e.META)90==b.which?this.sketcher.historyManager.undo():
89==b.which?this.sketcher.historyManager.redo():83==b.which?this.sketcher.toolbarManager.buttonSave.getElement().click():79==b.which?this.sketcher.toolbarManager.buttonOpen.getElement().click():78==b.which?this.sketcher.toolbarManager.buttonClear.getElement().click():187==b.which||61==b.which?this.sketcher.toolbarManager.buttonScalePlus.getElement().click():(189==b.which||109==b.which)&&this.sketcher.toolbarManager.buttonScaleMinus.getElement().click();
else if(37<=b.which&&40>=b.which){
	var c=new f.Point;
	switch(b.which){
	case 37:c.x=-10;
	break;
	case 38:c.y=-10;
	break;
	case 39:c.x=10;
	break;
	case 40:c.y=10
	}
	this.sketcher.historyManager.pushUndo(new g.MoveAction(this.sketcher.molecule.atoms,c))
	}
else if(187==b.which||189==b.which||61==b.which||109==b.which)null!=this.sketcher.hovering&&this.sketcher.hovering instanceof f.Atom&&this.sketcher.historyManager.pushUndo(new g.ChangeChargeAction(this.sketcher.hovering,187==b.which||61==b.which?1:-1));
else if(8==b.which||127==b.which)this.sketcher.stateManager.STATE_ERASE.handleDelete();

else if(48<=b.which&&57>=b.which){if(null!=this.sketcher.hovering){var k=b.which-48,c=[],l=[];
if(this.sketcher.hovering instanceof f.Atom)if(e.SHIFT){if(2<k&&9>k){var i=this.sketcher.molecule.getAngles(this.sketcher.hovering),h=3*j.PI/2;
0!=i.length&&(h=a.angleBetweenLargest(i).angle);
h=this.sketcher.stateManager.STATE_NEW_RING.getRing(this.sketcher.hovering,k,this.sketcher.specs.bondLength,h,!1);
-1==m(h[0],this.sketcher.molecule.atoms)&&c.push(h[0]);
this.bondExists(this.sketcher.hovering,h[0])||l.push(new f.Bond(this.sketcher.hovering,
h[0]));
i=1;
for(k=h.length;
i<k;
i++)-1==m(h[i],this.sketcher.molecule.atoms)&&c.push(h[i]),this.bondExists(h[i-1],h[i])||l.push(new f.Bond(h[i-1],h[i]));
this.bondExists(h[h.length-1],this.sketcher.hovering)||l.push(new f.Bond(h[h.length-1],this.sketcher.hovering))}}else{0==k&&(k=10);
for(var n=new f.Point(this.sketcher.hovering.x,this.sketcher.hovering.y),o=this.getOptimumAngle(this.sketcher.hovering),t=this.sketcher.hovering,i=0;
i<k;
i++){h=o+(1==i%2?j.PI/3:0);
n.x+=this.sketcher.specs.bondLength*j.cos(h);

n.y-=this.sketcher.specs.bondLength*j.sin(h);
for(var p=new f.Atom("C",n.x,n.y),s=Infinity,u=null,h=0,r=this.sketcher.molecule.atoms.length;
h<r;
h++){var v=this.sketcher.molecule.atoms[h].distance(p);
v<s&&(s=v,u=this.sketcher.molecule.atoms[h])}5>s?p=u:c.push(p);
this.bondExists(t,p)||l.push(new f.Bond(t,p));
t=p}}else if(this.sketcher.hovering instanceof f.Bond)if(e.SHIFT){if(2<k&&9>k){h=this.sketcher.stateManager.STATE_NEW_RING.getOptimalRing(this.sketcher.hovering,k);
i=this.sketcher.hovering.a2;
r=
this.sketcher.hovering.a1;
h[0]==this.sketcher.hovering.a1&&(i=this.sketcher.hovering.a1,r=this.sketcher.hovering.a2);
-1==m(h[1],this.sketcher.molecule.atoms)&&c.push(h[1]);
this.bondExists(i,h[1])||l.push(new f.Bond(i,h[1]));
i=2;
for(k=h.length;
i<k;
i++)-1==m(h[i],this.sketcher.molecule.atoms)&&c.push(h[i]),this.bondExists(h[i-1],h[i])||l.push(new f.Bond(h[i-1],h[i]));
this.bondExists(h[h.length-1],r)||l.push(new f.Bond(h[h.length-1],r))}}else 0<k&&4>k&&this.sketcher.historyManager.pushUndo(new g.ChangeBondAction(this.sketcher.hovering,
k,f.Bond.STEREO_NONE));
(0!=c.length||0!=l.length)&&this.sketcher.historyManager.pushUndo(new g.AddAction(this.sketcher.molecule,c,l))}}else if(65<=b.which&&90>=b.which&&null!=this.sketcher.hovering)if(this.sketcher.hovering instanceof f.Atom){i=String.fromCharCode(b.which);
l=c=null;
k=!1;
h=0;
for(r=d.length;
h<r;
h++)if(this.sketcher.hovering.label.charAt(0)==i)d[h]==this.sketcher.hovering.label?k=!0:d[h].charAt(0)==i&&(k&&null==l?l=d[h]:null==c&&(c=d[h]));
else if(d[h].charAt(0)==i){c=d[h];
break}p="C";

null!=l?p=l:null!=c&&(p=c);
this.sketcher.historyManager.pushUndo(new g.ChangeLabelAction(this.sketcher.hovering,p))}else this.sketcher.hovering instanceof f.Bond&&70==b.which&&this.sketcher.historyManager.pushUndo(new g.FlipBondAction(this.sketcher.hovering));
this.innerkeydown&&this.innerkeydown(b)};
b._State.prototype.keypress=function(a){this.innerkeypress&&this.innerkeypress(a)};
b._State.prototype.keyup=function(a){e.CANVAS_DRAGGING==this.sketcher&&null!=this.sketcher.lastPoint&&(a.p=this.sketcher.lastPoint,
this.sketcher.drag(a));
this.innerkeyup&&this.innerkeyup(a)}})(ChemDoodle.math,ChemDoodle.monitor,ChemDoodle.sketcher.actions,ChemDoodle.sketcher.states,ChemDoodle.structures,ChemDoodle.SYMBOLS,jQuery.inArray,Math);

(function(a,e){e.ChargeState=function(a){this.setup(a);
this.delta=1;
return!0};
e.ChargeState.prototype=new e._State;
e.ChargeState.prototype.innermouseup=function(){null!=this.sketcher.hovering&&this.sketcher.historyManager.pushUndo(new a.ChangeChargeAction(this.sketcher.hovering,this.delta))};
e.ChargeState.prototype.innermousemove=function(a){this.findHoveredObject(a,!0,!1)}})(ChemDoodle.sketcher.actions,ChemDoodle.sketcher.states);

(function(a,e,g,b){e.EraseState=function(a){this.setup(a);
return!0};
e.EraseState.prototype=new e._State;
e.EraseState.prototype.handleDelete=function(){if(null!=this.sketcher.hovering){if(this.sketcher.hovering instanceof g.Atom){for(var f=0,d=this.sketcher.molecule.atoms.length;
f<d;
f++)this.sketcher.molecule.atoms[f].visited=!1;
var e=[],j=[];
this.sketcher.hovering.visited=!0;
f=0;
for(d=this.sketcher.molecule.bonds.length;
f<d;
f++)if(this.sketcher.molecule.bonds[f].contains(this.sketcher.hovering)){var q=
[],c=[],k=new g.Queue;
for(k.enqueue(this.sketcher.molecule.bonds[f].getNeighbor(this.sketcher.hovering));
!k.isEmpty();
){var l=k.dequeue();
if(!l.visited){l.visited=!0;
q.push(l);
for(var i=0,h=this.sketcher.molecule.bonds.length;
i<h;
i++)this.sketcher.molecule.bonds[i].contains(l)&&!this.sketcher.molecule.bonds[i].getNeighbor(l).visited&&(k.enqueue(this.sketcher.molecule.bonds[i].getNeighbor(l)),c.push(this.sketcher.molecule.bonds[i]))}}e.push(q);
j.push(c)}q=c=-1;
f=0;
for(d=e.length;
f<d;
f++)e[f].length>
c&&(q=f,c=e[f].length);
if(-1<q){f=[];
d=[];
c=0;
for(k=this.sketcher.molecule.atoms.length;
c<k;
c++)-1==b(this.sketcher.molecule.atoms[c],e[q])&&f.push(this.sketcher.molecule.atoms[c]);
c=0;
for(k=this.sketcher.molecule.bonds.length;
c<k;
c++)-1==b(this.sketcher.molecule.bonds[c],j[q])&&d.push(this.sketcher.molecule.bonds[c]);
this.sketcher.historyManager.pushUndo(new a.DeleteAction(this.sketcher.molecule,f,d))}else this.sketcher.historyManager.pushUndo(new a.ClearAction(this.sketcher))}else this.sketcher.hovering instanceof
g.Bond&&null!=this.sketcher.hovering.ring&&(d=[],d[0]=this.sketcher.hovering,this.sketcher.historyManager.pushUndo(new a.DeleteAction(this.sketcher.molecule,null,d)));
this.sketcher.repaint()}};
e.EraseState.prototype.innermouseup=function(){this.handleDelete()};
e.EraseState.prototype.innermousemove=function(a){this.findHoveredObject(a,!0,!0)}})(ChemDoodle.sketcher.actions,ChemDoodle.sketcher.states,ChemDoodle.structures,jQuery.inArray);

(function(a,e){e.LabelState=function(a){this.setup(a);
this.label="C";
return!0};
e.LabelState.prototype=new e._State;
e.LabelState.prototype.innermouseup=function(){null!=this.sketcher.hovering&&this.sketcher.historyManager.pushUndo(new a.ChangeLabelAction(this.sketcher.hovering,this.label))};
e.LabelState.prototype.innermousemove=function(a){this.findHoveredObject(a,!0,!1)}})(ChemDoodle.sketcher.actions,ChemDoodle.sketcher.states);

(function(a,e){e.LonePairState=function(a){this.setup(a);
this.delta=1;
return!0};
e.LonePairState.prototype=new e._State;
e.LonePairState.prototype.innermouseup=function(){0>this.delta&&1>this.sketcher.hovering.numLonePair||null!=this.sketcher.hovering&&this.sketcher.historyManager.pushUndo(new a.ChangeLonePairAction(this.sketcher.hovering,this.delta))};
e.LonePairState.prototype.innermousemove=function(a){this.findHoveredObject(a,!0,!1)}})(ChemDoodle.sketcher.actions,ChemDoodle.sketcher.states);

(function(a,e,g){e.MoveState=function(a){this.setup(a);
this.action=null;
return!0};
e.MoveState.prototype=new e._State;
e.MoveState.prototype.innerdrag=function(b){if(null!=this.sketcher.hovering)if(null==this.action){var f=[],b=new g.Point(b.p.x,b.p.y);
this.sketcher.hovering instanceof g.Atom?(b.sub(this.sketcher.hovering),f[0]=this.sketcher.hovering):this.sketcher.hovering instanceof g.Bond&&(b.sub(this.sketcher.lastPoint),f[0]=this.sketcher.hovering.a1,f[1]=this.sketcher.hovering.a2);
this.action=
new a.MoveAction(f,b);
this.sketcher.historyManager.pushUndo(this.action)}else{b=new g.Point(b.p.x,b.p.y);
b.sub(this.sketcher.lastPoint);
this.action.dif.add(b);
for(var f=0,d=this.action.as.length;
f<d;
f++)this.action.as[f].add(b);
this.sketcher.molecule.check();
this.sketcher.repaint()}};
e.MoveState.prototype.innermousemove=function(a){this.findHoveredObject(a,!0,!0)};
e.MoveState.prototype.innermouseup=function(){this.action=null}})(ChemDoodle.sketcher.actions,ChemDoodle.sketcher.states,ChemDoodle.structures);

(function(a,e,g,b,f){g.NewBondState=function(a){this.setup(a);
this.bondOrder=1;
this.stereo=b.Bond.STEREO_NONE;
return!0};
g.NewBondState.prototype=new g._State;
g.NewBondState.prototype.incrementBondOrder=function(a){1==this.bondOrder&&this.stereo==b.Bond.STEREO_NONE?this.sketcher.historyManager.pushUndo(new e.ChangeBondAction(a)):a.bondOrder==this.bondOrder&&a.stereo==this.stereo?(1==a.bondOrder&&a.stereo!=b.Bond.STEREO_NONE||2==a.bondOrder&&a.stereo==b.Bond.STEREO_NONE)&&this.sketcher.historyManager.pushUndo(new e.FlipBondAction(a)):
this.sketcher.historyManager.pushUndo(new e.ChangeBondAction(a,this.bondOrder,this.stereo))};
g.NewBondState.prototype.innerdrag=function(d){if(this.sketcher.hovering instanceof b.Atom){if(15>d.p.distance(this.sketcher.hovering)){var g=this.getOptimumAngle(this.sketcher.hovering),d=this.sketcher.hovering.x+this.sketcher.specs.bondLength*f.cos(g),g=this.sketcher.hovering.y-this.sketcher.specs.bondLength*f.sin(g);
this.sketcher.tempAtom=new b.Atom("C",d,g,0)}else a.ALT&&a.SHIFT?this.sketcher.tempAtom=
new b.Atom("C",d.p.x,d.p.y,0):(g=this.sketcher.hovering.angle(d.p),d=this.sketcher.hovering.distance(d.p),a.SHIFT||(d=this.sketcher.specs.bondLength),a.ALT||(g=f.floor((g+f.PI/12)/(f.PI/6))*f.PI/6),this.sketcher.tempAtom=new b.Atom("C",this.sketcher.hovering.x+d*f.cos(g),this.sketcher.hovering.y-d*f.sin(g),0));
g=0;
for(d=this.sketcher.molecule.atoms.length;
g<d;
g++)5>this.sketcher.molecule.atoms[g].distance(this.sketcher.tempAtom)&&(this.sketcher.tempAtom.x=this.sketcher.molecule.atoms[g].x,this.sketcher.tempAtom.y=
this.sketcher.molecule.atoms[g].y,this.sketcher.tempAtom.isOverlap=!0);
this.sketcher.repaint()}};
g.NewBondState.prototype.innermousedown=function(a){this.sketcher.hovering instanceof b.Atom?(this.sketcher.hovering.isHover=!1,this.sketcher.hovering.isSelected=!0,this.drag(a)):this.sketcher.hovering instanceof b.Bond&&(this.sketcher.hovering.isHover=!1,this.incrementBondOrder(this.sketcher.hovering),this.sketcher.molecule.check(),this.sketcher.repaint())};
g.NewBondState.prototype.innermouseup=function(a){if(null!=
this.sketcher.tempAtom&&null!=this.sketcher.hovering){var f=[],j=[],g=!0;
if(this.sketcher.tempAtom.isOverlap){for(var c=0,k=this.sketcher.molecule.atoms.length;
c<k;
c++)5>this.sketcher.molecule.atoms[c].distance(this.sketcher.tempAtom)&&(this.sketcher.tempAtom=this.sketcher.molecule.atoms[c]);
c=this.getBond(this.sketcher.hovering,this.sketcher.tempAtom);
null!=c&&(this.incrementBondOrder(c),g=!1)}else f.push(this.sketcher.tempAtom);
g&&(j[0]=new b.Bond(this.sketcher.hovering,this.sketcher.tempAtom,this.bondOrder),
j[0].stereo=this.stereo,this.sketcher.historyManager.pushUndo(new e.AddAction(this.sketcher.molecule,f,j)))}this.sketcher.tempAtom=null;
this.sketcher.isMobile||this.mousemove(a)};
g.NewBondState.prototype.innermousemove=function(a){null==this.sketcher.tempAtom&&this.findHoveredObject(a,!0,!0)}})(ChemDoodle.monitor,ChemDoodle.sketcher.actions,ChemDoodle.sketcher.states,ChemDoodle.structures,Math);

(function(a,e,g,b,f,d,m){b.NewRingState=function(a){this.setup(a);
this.numSides=6;
this.unsaturated=!1;
return!0};
b.NewRingState.prototype=new b._State;
b.NewRingState.prototype.getRing=function(a,b,c,d,g){for(var e=m.PI-2*m.PI/b,d=d+e/2,h=[],n=0;
n<b-1;
n++){var o=0==n?new f.Atom("C",a.x,a.y):new f.Atom("C",h[h.length-1].x,h[h.length-1].y);
o.x+=c*m.cos(d);
o.y-=c*m.sin(d);
h.push(o);
d+=m.PI+e}a=0;
for(b=this.sketcher.molecule.atoms.length;
a<b;
a++)this.sketcher.molecule.atoms[a].isOverlap=!1;
n=0;
for(c=h.length;
n<
c;
n++){d=Infinity;
e=null;
a=0;
for(b=this.sketcher.molecule.atoms.length;
a<b;
a++)o=this.sketcher.molecule.atoms[a].distance(h[n]),o<d&&(d=o,e=this.sketcher.molecule.atoms[a]);
5>d&&(h[n]=e,g&&(e.isOverlap=!0))}return h};
b.NewRingState.prototype.getOptimalRing=function(a,b){for(var c=m.PI/2-m.PI/b,f=a.a1.distance(a.a2),d=this.getRing(a.a1,b,f,a.a1.angle(a.a2)-c,!1),c=this.getRing(a.a2,b,f,a.a2.angle(a.a1)-c,!1),g=f=0,e=1,n=d.length;
e<n;
e++)for(var o=0,t=this.sketcher.molecule.atoms.length;
o<t;
o++)var p=
this.sketcher.molecule.atoms[o].distance(d[e]),s=this.sketcher.molecule.atoms[o].distance(c[e]),f=f+m.min(1E8,1/(p*p)),g=g+m.min(1E8,1/(s*s));
return f<g?d:c};
b.NewRingState.prototype.innerdrag=function(b){if(this.sketcher.hovering instanceof f.Atom){var g=0,c=0;
if(15>b.p.distance(this.sketcher.hovering))b=this.sketcher.molecule.getAngles(this.sketcher.hovering),g=0==b.length?3*m.PI/2:a.angleBetweenLargest(b).angle,c=this.sketcher.specs.bondLength;
else if(g=this.sketcher.hovering.angle(b.p),c=this.sketcher.hovering.distance(b.p),
!e.ALT||!e.SHIFT)e.SHIFT||(c=this.sketcher.specs.bondLength),e.ALT||(g=m.floor((g+m.PI/12)/(m.PI/6))*m.PI/6);
this.sketcher.tempRing=this.getRing(this.sketcher.hovering,this.numSides,c,g,!0);
this.sketcher.repaint()}else if(this.sketcher.hovering instanceof f.Bond){g=a.distanceFromPointToLineInclusive(b.p,this.sketcher.hovering.a1,this.sketcher.hovering.a2);
c=null;
if(-1!=g&&7>=g)c=this.getOptimalRing(this.sketcher.hovering,this.numSides);
else{for(var c=m.PI/2-m.PI/this.numSides,k=this.sketcher.hovering.a1.distance(this.sketcher.hovering.a2),
g=this.getRing(this.sketcher.hovering.a1,this.numSides,k,this.sketcher.hovering.a1.angle(this.sketcher.hovering.a2)-c,!1),c=this.getRing(this.sketcher.hovering.a2,this.numSides,k,this.sketcher.hovering.a2.angle(this.sketcher.hovering.a1)-c,!1),l=new f.Point,k=new f.Point,i=1,h=g.length;
i<h;
i++)l.add(g[i]),k.add(c[i]);
l.x/=g.length-1;
l.y/=g.length-1;
k.x/=c.length-1;
k.y/=c.length-1;
l=l.distance(b.p);
b=k.distance(b.p);
l<b&&(c=g)}b=1;
for(g=c.length;
b<g;
b++)-1!=d(c[b],this.sketcher.molecule.atoms)&&(c[b].isOverlap=
!0);
this.sketcher.tempRing=c;
this.sketcher.repaint()}};
b.NewRingState.prototype.innermousedown=function(a){null!=this.sketcher.hovering&&(this.sketcher.hovering.isHover=!1,this.sketcher.hovering.isSelected=!0,this.drag(a))};
b.NewRingState.prototype.innermouseup=function(a){if(null!=this.sketcher.tempRing&&null!=this.sketcher.hovering){var b=[],c=[];
if(this.sketcher.hovering instanceof f.Atom){-1==d(this.sketcher.tempRing[0],this.sketcher.molecule.atoms)&&b.push(this.sketcher.tempRing[0]);
this.bondExists(this.sketcher.hovering,
this.sketcher.tempRing[0])||c.push(new f.Bond(this.sketcher.hovering,this.sketcher.tempRing[0]));
for(var e=1,l=this.sketcher.tempRing.length;
e<l;
e++)-1==d(this.sketcher.tempRing[e],this.sketcher.molecule.atoms)&&b.push(this.sketcher.tempRing[e]),this.bondExists(this.sketcher.tempRing[e-1],this.sketcher.tempRing[e])||c.push(new f.Bond(this.sketcher.tempRing[e-1],this.sketcher.tempRing[e],1==e%2&&this.unsaturated?2:1));
this.bondExists(this.sketcher.tempRing[this.sketcher.tempRing.length-1],this.sketcher.hovering)||
c.push(new f.Bond(this.sketcher.tempRing[this.sketcher.tempRing.length-1],this.sketcher.hovering,this.unsaturated?2:1))}else if(this.sketcher.hovering instanceof f.Bond){var e=this.sketcher.hovering.a2,i=this.sketcher.hovering.a1;
this.sketcher.tempRing[0]==this.sketcher.hovering.a1&&(e=this.sketcher.hovering.a1,i=this.sketcher.hovering.a2);
-1==d(this.sketcher.tempRing[1],this.sketcher.molecule.atoms)&&b.push(this.sketcher.tempRing[1]);
this.bondExists(e,this.sketcher.tempRing[1])||c.push(new f.Bond(e,
this.sketcher.tempRing[1]));
e=2;
for(l=this.sketcher.tempRing.length;
e<l;
e++)-1==d(this.sketcher.tempRing[e],this.sketcher.molecule.atoms)&&b.push(this.sketcher.tempRing[e]),this.bondExists(this.sketcher.tempRing[e-1],this.sketcher.tempRing[e])||c.push(new f.Bond(this.sketcher.tempRing[e-1],this.sketcher.tempRing[e],0==e%2&&this.unsaturated?2:1));
this.bondExists(this.sketcher.tempRing[this.sketcher.tempRing.length-1],i)||c.push(new f.Bond(this.sketcher.tempRing[this.sketcher.tempRing.length-1],i))}(0!=
b.length||0!=c.length)&&this.sketcher.historyManager.pushUndo(new g.AddAction(this.sketcher.molecule,b,c))}b=0;
for(c=this.sketcher.molecule.atoms.length;
b<c;
b++)this.sketcher.molecule.atoms[b].isOverlap=!1;
this.sketcher.tempRing=null;
this.sketcher.isMobile||this.mousemove(a)};
b.NewRingState.prototype.innermousemove=function(a){null==this.sketcher.tempAtom&&this.findHoveredObject(a,!0,!0)}})(ChemDoodle.math,ChemDoodle.monitor,ChemDoodle.sketcher.actions,ChemDoodle.sketcher.states,ChemDoodle.structures,
jQuery.inArray,Math);
(function(a){a.StateManager=function(e){this.STATE_NEW_BOND=new a.NewBondState(e);
this.STATE_NEW_RING=new a.NewRingState(e);
this.STATE_CHARGE=new a.ChargeState(e);
this.STATE_LONE_PAIR=new a.LonePairState(e);
this.STATE_MOVE=new a.MoveState(e);
this.STATE_ERASE=new a.EraseState(e);
this.STATE_LABEL=new a.LabelState(e);
this.currentState=this.STATE_NEW_BOND;
return!0}})(ChemDoodle.sketcher.states);

(function(a,e){a.Button=function(a,b,f,d,e){this.id=a;
this.iconPath=b;
this.icon=f;
this.toggle=!1;
this.tooltip=d?d:"";
this.func=e?e:null;
return!0};
a.Button.prototype.getElement=function(){return e("#"+this.id)};
a.Button.prototype.getSource=function(a){var b=[];
this.toggle?(b.push('<input type="radio" name="'),b.push(a),b.push('" id="'),b.push(this.id),b.push('"><label for="'),b.push(this.id),b.push('"><img id="'),b.push(this.id),b.push('_icon" title="'),b.push(this.tooltip),b.push('" src="'),b.push(this.iconPath),
b.push(this.icon),b.push('.png"></label>')):(b.push('<button id="'),b.push(this.id),b.push('" onclick="return false;"><img title="'),b.push(this.tooltip),b.push('" width="20" height="20" src="'),b.push(this.iconPath),b.push(this.icon),b.push('.png"></button>'));
return b.join("")};
a.Button.prototype.setup=function(a){(!this.toggle||a)&&this.getElement().button();
this.getElement().click(this.func)};
a.Button.prototype.disable=function(){var a=this.getElement();
a.mouseout();
a.button("disable")};
a.Button.prototype.enable=
function(){this.getElement().button("enable")};
a.Button.prototype.select=function(){var a=this.getElement();
a.attr("checked",!0);
a.button("refresh")}})(ChemDoodle.sketcher.gui.desktop,jQuery);

(function(a,e){a.ButtonSet=function(a){this.id=a;
this.buttons=[];
this.buttonGroup="main_group";
return this.toggle=!0};
a.ButtonSet.prototype.getElement=function(){return e("#"+this.id)};
a.ButtonSet.prototype.getSource=function(){var a=[];
a.push('<span id="');
a.push(this.id);
a.push('">');
for(var b=0,f=this.buttons.length;
b<f;
b++)this.toggle&&(this.buttons[b].toggle=!0),a.push(this.buttons[b].getSource(this.buttonGroup));
null!=this.dropDown&&a.push(this.dropDown.getButtonSource());
a.push("</span>");

null!=this.dropDown&&a.push(this.dropDown.getHiddenSource());
return a.join("")};
a.ButtonSet.prototype.setup=function(){this.getElement().buttonset();
for(var a=0,b=this.buttons.length;
a<b;
a++)this.buttons[a].setup(!1);
null!=this.dropDown&&this.dropDown.setup()};
a.ButtonSet.prototype.addDropDown=function(e,b){this.dropDown=new a.DropDown(this.id+"_dd",b,e,this.buttons[this.buttons.length-1])}})(ChemDoodle.sketcher.gui.desktop,jQuery);

(function(a,e,g){a.Dialog=function(a,f){this.id=a;
this.title=f?f:"Information";
this.afterMessage=this.message=this.buttons=null;
this.includeTextArea=!1;
return!0};
a.Dialog.prototype.getElement=function(){return e("#"+this.id)};
a.Dialog.prototype.getTextArea=function(){return e("#"+this.id+"_ta")};
a.Dialog.prototype.setup=function(){var a=[];
a.push('<div style="font-size:12px;" id="');
a.push(this.id);
a.push('" title="');
a.push(this.title);
a.push('">');
null!=this.message&&(a.push("<p>"),a.push(this.message),
a.push("</p>"));
this.includeTextArea&&(a.push('<textarea style="font-family:\'Courier New\';" id="'),a.push(this.id),a.push('_ta" cols="55" rows="10"></textarea>'));
null!=this.afterMessage&&(a.push("<p>"),a.push(this.afterMessage),a.push("</p>"));
a.push("</div>");
g.writeln(a.join(""));
this.getElement().dialog({autoOpen:!1,width:435,buttons:this.buttons})}})(ChemDoodle.sketcher.gui.desktop,jQuery,document);

(function(a,e,g,b){e.MolGrabberDialog=function(a){this.id=a;
this.title="MolGrabber";
this.afterMessage=this.message=this.buttons=null;
this.includeTextArea=!1;
return!0};
e.MolGrabberDialog.prototype=new e.Dialog;
e.MolGrabberDialog.prototype.setup=function(){var a=[];
a.push('<div style="font-size:12px;text-align:center;" id="');
a.push(this.id);
a.push('" title="');
a.push(this.title);
a.push('">');
null!=this.message&&(a.push("<p>"),a.push(this.message),a.push("</p>"));
b.writeln(a.join(""));
this.canvas=new ChemDoodle.MolGrabberCanvas(this.id+"_mg",200,200);
a=[];
null!=this.afterMessage&&(a.push("<p>"),a.push(this.afterMessage),a.push("</p>"));
a.push("</div>");
b.writeln(a.join(""));
this.getElement().dialog({autoOpen:!1,width:250,buttons:this.buttons})}})(ChemDoodle,ChemDoodle.sketcher.gui.desktop,jQuery,document);

(function(a,e,g,b){e.PeriodicTableDialog=function(a){this.id=a;
this.title="Periodic Table";
this.afterMessage=this.message=this.buttons=null;
this.includeTextArea=!1;
return!0};
e.PeriodicTableDialog.prototype=new e.Dialog;
e.PeriodicTableDialog.prototype.setup=function(){var a=[];
a.push('<div style="text-align:center;" id="');
a.push(this.id);
a.push('" title="');
a.push(this.title);
a.push('">');
b.writeln(a.join(""));
this.canvas=new ChemDoodle.PeriodicTableCanvas(this.id+"_pt",20);
b.writeln("</div>");
this.getElement().dialog({autoOpen:!1,
width:400,buttons:this.buttons})}})(ChemDoodle,ChemDoodle.sketcher.gui.desktop,jQuery,document);

(function(a,e,g,b){e.SaveFileDialog=function(a,b){this.id=a;
this.sketcher=b;
this.title="Save File";
this.afterMessage=this.buttons=null;
this.includeTextArea=!1;
return!0};
e.SaveFileDialog.prototype=new e.Dialog;
e.SaveFileDialog.prototype.clear=function(){g("#"+this.id+"_link").html("The file link will appear here.")};
e.SaveFileDialog.prototype.setup=function(){var a=[];
a.push('<div style="font-size:12px;" id="');
a.push(this.id);
a.push('" title="');
a.push(this.title);
a.push('">');
a.push("<p>Select the file format to save your structure to and click on the <strong>Generate File</strong> button.</p>");

a.push('<select id="');
a.push(this.id);
a.push('_select">');
a.push('<option value="sk2">ACD/ChemSketch Document {sk2}');
a.push('<option value="ros">Beilstein ROSDAL {ros}');
a.push('<option value="cdx">Cambridgesoft ChemDraw Exchange {cdx}');
a.push('<option value="cdxml">Cambridgesoft ChemDraw XML {cdxml}');
a.push('<option value="mrv">ChemAxon Marvin Document {mrv}');
a.push('<option value="cml">Chemical Markup Language {cml}');
a.push('<option value="smiles">Daylight SMILES {smiles}');
a.push('<option value="icl" selected>iChemLabs ChemDoodle Document {icl}');

a.push('<option value="inchi">IUPAC InChI {inchi}');
a.push('<option value="jdx">IUPAC JCAMP-DX {jdx}');
a.push('<option value="skc">MDL ISIS Sketch {skc}');
a.push('<option value="tgf">MDL ISIS Sketch Transportable Graphics File {tgf}');
a.push('<option value="mol">MDL MOLFile {mol}');
a.push('<option value="sdf">MDL SDFile {sdf}');
a.push('<option value="jme">Molinspiration JME String {jme}');
a.push('<option value="pdb">RCSB Protein Data Bank {pdb}');
a.push('<option value="mmd">Schr&ouml;dinger Macromodel {mmd}');

a.push('<option value="mae">Schr&ouml;dinger Maestro {mae}');
a.push('<option value="smd">Standard Molecular Data {smd}');
a.push('<option value="mol2">Tripos Mol2 {mol2}');
a.push('<option value="sln">Tripos SYBYL SLN {sln}');
a.push('<option value="xyz">XYZ {xyz}');
a.push("</select>");
a.push('<button id="');
a.push(this.id);
a.push('_button">');
a.push("Generate File</button>");
a.push("<p>When the file is written, a link will appear in the red-bordered box below, right-click on the link and choose the browser's <strong>Save As...</strong> function to save the file to your computer.</p>");

a.push('<div style="width:100%;height:30px;border:1px solid #c10000;text-align:center;" id="');
a.push(this.id);
a.push('_link">The file link will appear here.</div>');
a.push('<p><a href="http://www.chemdoodle.com" target="_blank">How do I use these files?</a></p>');
a.push("</div>");
b.writeln(a.join(""));
var d=this;
g("#"+this.id+"_button").click(function(){g("#"+d.id+"_link").html("Generating file, please wait...");
ChemDoodle.iChemLabs.saveFile(d.sketcher.molecule,g("#"+d.id+"_select").val(),function(a){g("#"+
d.id+"_link").html('<a href="'+a+'"><span style="text-decoration:underline;">File is generated. Right-click on this link and Save As...</span></a>')})});
this.getElement().dialog({autoOpen:!1,width:435,buttons:d.buttons})}})(ChemDoodle,ChemDoodle.sketcher.gui.desktop,jQuery,document);

var newdisp='';

function insert_dialog(){
	copymoltext();
	savepng();
}
function copymoltext(){
	document.getElementById("moltext_dialog").value=document.getElementById("sketcher_save_dialog_ta").value;
}
function savepng(){
	document.getElementById("molfig_dialog").value=document.getElementById("sketcher").toDataURL("image/png");
}

(function(a,e,g,b,f){  //DIALOGS- a=CHEMDOODLE 
	g.DialogManager=function(d){
		d.useServices?this.saveDialog=new b.SaveFileDialog(d.id+"_save_dialog",d):
			(
				this.saveDialog=new b.Dialog(d.id+"_save_dialog","Save Molecule"),
				this.saveDialog.message="Here is your molecule in mol format.",
				this.saveDialog.includeTextArea=!0,
				newdisp+='<form action="'+cgidir+'uploadmol.py" method="post">',
				newdisp+='<input type="hidden" name="moltext_dialog" id="moltext_dialog" value="default" /><input type="hidden" name="molfig_dialog" id="molfig_dialog" value="default" />',
				newdisp+='</form>'),
				this.saveDialog.afterMessage=newdisp;
		this.saveDialog.setup();
		this.loadDialog=new b.Dialog(d.id+"_load_dialog","Load Molecule");
		this.loadDialog.message="Copy and paste the contents of a MOL file in the textarea below and then press the <strong>Load</strong> button.";
		this.loadDialog.includeTextArea=!0;
		this.loadDialog.afterMessage='';
		var g=this;
		this.loadDialog.buttons={Load:function(){f(this).dialog("close");
			var b=a.readMOL(g.loadDialog.getTextArea().val());
			b.atoms.length!=0?d.historyManager.pushUndo(new e.SwitchMoleculeAction(d,b)):alert("No chemical content was recognized.")}};

		this.loadDialog.setup();
		this.searchDialog=new b.MolGrabberDialog(d.id+"_search_dialog");
this.searchDialog.buttons={Load:function(){f(this).dialog("close");
var a=g.searchDialog.canvas.molecule;
a!=null&&a.atoms.length!=0&&a!=d.molecule&&d.historyManager.pushUndo(new e.SwitchMoleculeAction(d,a))}};
this.searchDialog.setup();
this.periodicTableDialog=new b.PeriodicTableDialog(d.id+"_periodicTable_dialog");

this.periodicTableDialog.buttons={Close:function(){f(this).dialog("close")}};
this.periodicTableDialog.setup();
this.periodicTableDialog.canvas.click=function(){if(this.hovered!=null){this.selected=this.hovered;
var a=this.getHoveredElement();
d.stateManager.currentState=d.stateManager.STATE_LABEL;
d.stateManager.STATE_LABEL.label=a.symbol;
this.repaint()}};
this.calculateDialog=new b.Dialog(d.id+"_calculate_dialog","Calculations");
this.calculateDialog.includeTextArea=!0;
this.calculateDialog.afterMessage=
'<a href="http://www.chemdoodle.com" target="_blank">Want more calculations?</a>';
this.calculateDialog.setup();
return!0}})(ChemDoodle,ChemDoodle.sketcher.actions,ChemDoodle.sketcher.gui,ChemDoodle.sketcher.gui.desktop,jQuery);

(function(a,e,g){a.DropDown=function(b,e,d,g){this.id=b;
this.iconPath=e;
this.tooltip=d;
this.dummy=g;
this.buttonSet=new a.ButtonSet(b+"_set");
this.buttonSet.buttonGroup=d;
this.defaultButton=null;
return!0};
a.DropDown.prototype.getButtonSource=function(){var a=[];
a.push('<button id="');
a.push(this.id);
a.push('" onclick="return false;"><img title="');
a.push(this.tooltip);
a.push('" src="');
a.push(this.iconPath);
a.push('arrowDown.png"></button>');
return a.join("")};
a.DropDown.prototype.getHiddenSource=
function(){var a=[];
a.push('<div style="display:none;position:absolute;z-index:10;border:1px #C1C1C1 solid;background:#F5F5F5;padding:5px;border-bottom-left-radius:5px;-moz-border-radius-bottomleft:5px;border-bottom-right-radius:5px;-moz-border-radius-bottomright:5px;" id="');
a.push(this.id);
a.push('_hidden">');
a.push(this.buttonSet.getSource());
a.push("</div>");
return a.join("")};
a.DropDown.prototype.setup=function(){null==this.defaultButton&&(this.defaultButton=this.buttonSet.buttons[0]);
var a="#"+this.id;
e(a).button();
e(a+"_hidden").hide();
e(a).click(function(){var d=e(a+"_hidden");
d.show().position({my:"center top",at:"center bottom",of:this,collision:"fit"});
e(g).one("click",function(){d.hide()});
return false});
this.buttonSet.setup();
var f=this;
e.each(this.buttonSet.buttons,function(a){f.buttonSet.buttons[a].getElement().click(function(){f.dummy.absorb(f.buttonSet.buttons[a]);
f.dummy.select();
f.dummy.func()})});
f.dummy.absorb(this.defaultButton);
this.defaultButton.select()}})(ChemDoodle.sketcher.gui.desktop,jQuery,document);


(function(a,e){a.DummyButton=function(a,b,e,d){this.id=a;
this.iconPath=b;
this.icon=e;
this.toggle=!1;
this.tooltip=d?d:"";
this.func=null;
return!0};
a.DummyButton.prototype=new a.Button;
a.DummyButton.prototype.setup=function(){var a=this;
this.getElement().click(function(){a.func()})};
a.DummyButton.prototype.absorb=function(a){e("#"+this.id+"_icon").attr("src",a.iconPath+a.icon+".png");
e("#"+this.id).button("refresh");
this.func=a.func}})(ChemDoodle.sketcher.gui.desktop,jQuery);

(function(a,e,g,b,f,d,m,j,q){
	f.ToolbarManager=function(c){
		this.sketcher=c; //c = SKETCHER a=CHEMDOODLEWEB q=DOCUMENT
		this.buttonOpen=new d.Button(c.id+"_button_open",c.iconPath,"open20","Open",function(){c.dialogManager.loadDialog.getTextArea().val("");
			c.dialogManager.loadDialog.getElement().dialog("open")});
		this.buttonSave=new d.Button(c.id+"_button_save",c.iconPath,"save20","Save",function(){c.useServices?c.dialogManager.saveDialog.clear():(c.dialogManager.saveDialog.getTextArea().val(a.writeMOL(c.molecule)));
			c.dialogManager.saveDialog.getElement().dialog("open")});

this.buttonSearch=new d.Button(c.id+"_button_search",c.iconPath,"search20","Search",function(){c.dialogManager.searchDialog.getElement().dialog("open")});
this.buttonCalculate=new d.Button(c.id+"_button_calculate",c.iconPath,"calculate20","Calculate",function(){e.calculate(c.molecule,"mf,mw,miw,deg_unsat,hba,hbd,pol_miller,cmr,tpsa,xlogp2".split(","),function(a){function b(a,c,d){j.push(a);
	j.push(": ");
	for(a=a.length+2;a<30;a++)
	j.push(" ");
	j.push(c);
	j.push(" ");
	j.push(d);
	j.push("\n")}
var j=[];
b("Molecular Formula",a.mf,"");
b("Molecular Mass",a.mw,"amu");
b("Monoisotopic Mass",a.miw,"amu");
b("Degree of Unsaturation",a.deg_unsat,"");
b("Hydrogen Bond Acceptors",a.hba,"");
b("Hydrogen Bond Donors",a.hbd,"");
b("Molecular Polarizability",a.pol_miller,"\u00c5\u00b3");
b("Molar Refractivity",a.cmr,"cm\u00b3/mol");
b("Polar Surface Area",a.tpsa,"\u00c5\u00b2");
b("logP",a.xlogp2,"");
c.dialogManager.calculateDialog.getTextArea().val(j.join(""));
c.dialogManager.calculateDialog.getElement().dialog("open")})});
this.buttonMove=new d.Button(c.id+"_button_move",c.iconPath,"move20","Move",function(){c.stateManager.currentState=c.stateManager.STATE_MOVE});
this.buttonMove.toggle=!0;
this.buttonErase=new d.Button(c.id+"_button_erase",c.iconPath,"erase20","Erase",function(){c.stateManager.currentState=c.stateManager.STATE_ERASE});
this.buttonErase.toggle=!0;
this.buttonCenter=new d.Button(c.id+"_button_center",c.iconPath,"CenterSketch","Center Molecule",function(){c.center(c.molecule);});
this.buttonClear=new d.Button(c.id+"_button_clear",c.iconPath,"clear20","Clear",function(){var a=!0;
if(1==c.molecule.atoms.length){var j=c.molecule.atoms[0];
"C"==j.label&&0==
j.charge&&-1==j.mass&&(a=!1)}a&&c.historyManager.pushUndo(new b.ClearAction(c))});
this.buttonClean=new d.Button(c.id+"_button_clean",c.iconPath,"optimize20","Clean",function(){e.contactServer("optimize",{mol:g.toJSONDummy(c.molecule),dimension:2},function(a){var a=g.fromJSONDummy(a.mol),j=a.getCenter(),d=new m.Point(c.width/2,c.height/2);
d.sub(j);
for(var j=0,e=a.atoms.length;
j<e;
j++)a.atoms[j].add(d);
c.historyManager.pushUndo(new b.ChangeCoordinatesAction(c.molecule.atoms,a.atoms))})});
this.makeScaleSet(this);
this.makeHistorySet(this);
this.makeLabelSet(this);
this.makeBondSet(this);
this.makeRingSet(this);
this.makeAttributeSet(this);
return!0};
f.ToolbarManager.prototype.write=function(){var a=[];
a.push(this.buttonOpen.getSource());
a.push(this.buttonSave.getSource());
this.sketcher.useServices&&a.push(this.buttonSearch.getSource());
a.push(this.scaleSet.getSource());
a.push(this.buttonCenter.getSource());
a.push(this.buttonClear.getSource());
a.push(this.buttonErase.getSource("main_group"));
a.push(this.buttonMove.getSource("main_group"));
this.sketcher.useServices&&
a.push(this.buttonClean.getSource());
a.push(this.historySet.getSource());
this.sketcher.useServices&&a.push(this.buttonCalculate.getSource());
a.push("<br>");
a.push(this.labelSet.getSource());
a.push(this.bondSet.getSource());
a.push(this.ringSet.getSource());
a.push(this.attributeSet.getSource());
a.push("<br>");
q.write(a.join(""))};
f.ToolbarManager.prototype.setup=function(){this.buttonOpen.setup();
this.buttonSave.setup();
this.sketcher.useServices&&this.buttonSearch.setup();
this.scaleSet.setup();
this.buttonCenter.setup();
this.buttonClear.setup();

this.buttonErase.setup(!0);
this.buttonMove.setup(!0);
this.sketcher.useServices&&this.buttonClean.setup();
this.historySet.setup();
this.sketcher.useServices&&this.buttonCalculate.setup();
this.labelSet.setup();
this.bondSet.setup();
this.ringSet.setup();
this.attributeSet.setup();
this.buttonSingle.select();
this.buttonUndo.disable();
this.buttonRedo.disable()};
f.ToolbarManager.prototype.makeScaleSet=function(a){this.scaleSet=new d.ButtonSet(a.sketcher.id+"_buttons_scale");
this.scaleSet.toggle=!1;
this.buttonScalePlus=
new d.Button(a.sketcher.id+"_button_scale_plus",a.sketcher.iconPath,"zoomIn20","Increase Scale",function(){a.sketcher.specs.scale*=1.5;
a.sketcher.checkScale();
a.sketcher.repaint()});
this.scaleSet.buttons.push(this.buttonScalePlus);
this.buttonScaleMinus=new d.Button(a.sketcher.id+"_button_scale_minus",a.sketcher.iconPath,"zoomOut20","Decrease Scale",function(){a.sketcher.specs.scale/=1.5;
a.sketcher.checkScale();
a.sketcher.repaint()});
this.scaleSet.buttons.push(this.buttonScaleMinus)};
f.ToolbarManager.prototype.makeHistorySet=
function(a){this.historySet=new d.ButtonSet(a.sketcher.id+"_buttons_history");
this.historySet.toggle=!1;
this.buttonUndo=new d.Button(a.sketcher.id+"_button_undo",a.sketcher.iconPath,"undo20","Undo",function(){a.sketcher.historyManager.undo()});
this.historySet.buttons.push(this.buttonUndo);
this.buttonRedo=new d.Button(a.sketcher.id+"_button_redo",a.sketcher.iconPath,"redo20","Redo",function(){a.sketcher.historyManager.redo()});
this.historySet.buttons.push(this.buttonRedo)};
f.ToolbarManager.prototype.makeLabelSet=
function(a){this.labelSet=new d.ButtonSet(a.sketcher.id+"_buttons_label");
this.buttonLabel=new d.DummyButton(a.sketcher.id+"_button_label",a.sketcher.iconPath,"Carbon","Set Label");
this.labelSet.buttons.push(this.buttonLabel);
this.labelSet.addDropDown("More Labels",a.sketcher.iconPath);
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_label_h",a.sketcher.iconPath,"Hydrogen","Hydrogen",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LABEL;

a.sketcher.stateManager.STATE_LABEL.label="H"}));
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_label_c",a.sketcher.iconPath,"Carbon","Carbon",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LABEL;
a.sketcher.stateManager.STATE_LABEL.label="C"}));
this.labelSet.dropDown.defaultButton=this.labelSet.dropDown.buttonSet.buttons[this.labelSet.dropDown.buttonSet.buttons.length-1];
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+
"_button_label_n",a.sketcher.iconPath,"Nitrogen","Nitrogen",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LABEL;
a.sketcher.stateManager.STATE_LABEL.label="N"}));
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_label_o",a.sketcher.iconPath,"Oxygen","Oxygen",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LABEL;
a.sketcher.stateManager.STATE_LABEL.label="O"}));
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+
"_button_label_f",a.sketcher.iconPath,"Fluorine","Fluorine",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LABEL;
a.sketcher.stateManager.STATE_LABEL.label="F"}));
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_label_cl",a.sketcher.iconPath,"Chlorine","Chlorine",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LABEL;
a.sketcher.stateManager.STATE_LABEL.label="Cl"}));
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+
"_button_label_br",a.sketcher.iconPath,"Bromine","Bromine",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LABEL;
a.sketcher.stateManager.STATE_LABEL.label="Br"}));
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_label_r",a.sketcher.iconPath,"R-Group","R-Group",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LABEL;
a.sketcher.stateManager.STATE_LABEL.label="R"}));
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+
"_button_label_p",a.sketcher.iconPath,"Phosphorus","Phosphorus",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LABEL;
a.sketcher.stateManager.STATE_LABEL.label="P"}));
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_label_s",a.sketcher.iconPath,"Sulfur","Sulfur",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LABEL;
a.sketcher.stateManager.STATE_LABEL.label="S"}));
this.labelSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+
"_button_label_pt",a.sketcher.iconPath,"periodicTable20","Choose Symbol",function(){for(var b=0,j=a.sketcher.dialogManager.periodicTableDialog.canvas.cells.length;
b<j;
b++){var d=a.sketcher.dialogManager.periodicTableDialog.canvas.cells[b];
if(d.element.symbol==a.sketcher.stateManager.STATE_LABEL.label){a.sketcher.dialogManager.periodicTableDialog.canvas.selected=d;
a.sketcher.dialogManager.periodicTableDialog.canvas.repaint();
break}}a.sketcher.dialogManager.periodicTableDialog.getElement().dialog("open")}))};

f.ToolbarManager.prototype.makeBondSet=function(a){this.bondSet=new d.ButtonSet(a.sketcher.id+"_buttons_bond");
this.buttonSingle=new d.Button(a.sketcher.id+"_button_bond_single",a.sketcher.iconPath,"SingleBond","Single Bond",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_BOND;
a.sketcher.stateManager.STATE_NEW_BOND.bondOrder=1;
a.sketcher.stateManager.STATE_NEW_BOND.stereo=m.Bond.STEREO_NONE});
this.bondSet.buttons.push(this.buttonSingle);
this.buttonRecessed=new d.Button(a.sketcher.id+
"_button_bond_recessed",a.sketcher.iconPath,"RecessedBond","Recessed Bond",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_BOND;
a.sketcher.stateManager.STATE_NEW_BOND.bondOrder=1;
a.sketcher.stateManager.STATE_NEW_BOND.stereo=m.Bond.STEREO_RECESSED});
this.bondSet.buttons.push(this.buttonRecessed);
this.buttonProtruding=new d.Button(a.sketcher.id+"_button_bond_protruding",a.sketcher.iconPath,"ProtrudingBond","Protruding Bond",function(){a.sketcher.stateManager.currentState=
a.sketcher.stateManager.STATE_NEW_BOND;
a.sketcher.stateManager.STATE_NEW_BOND.bondOrder=1;
a.sketcher.stateManager.STATE_NEW_BOND.stereo=m.Bond.STEREO_PROTRUDING});
this.bondSet.buttons.push(this.buttonProtruding);
this.buttonDouble=new d.Button(a.sketcher.id+"_button_bond_double",a.sketcher.iconPath,"DoubleBond","Double Bond",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_BOND;
a.sketcher.stateManager.STATE_NEW_BOND.bondOrder=2;
a.sketcher.stateManager.STATE_NEW_BOND.stereo=
m.Bond.STEREO_NONE});
this.bondSet.buttons.push(this.buttonDouble);
this.buttonBond=new d.DummyButton(a.sketcher.id+"_button_bond",a.sketcher.iconPath,"TripleBond","Other Bond");
this.bondSet.buttons.push(this.buttonBond);
this.bondSet.addDropDown("More Bonds",a.sketcher.iconPath);
this.bondSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_bond_triple",a.sketcher.iconPath,"TripleBond","Triple Bond",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_BOND;

a.sketcher.stateManager.STATE_NEW_BOND.bondOrder=3;
a.sketcher.stateManager.STATE_NEW_BOND.stereo=m.Bond.STEREO_NONE}));
this.bondSet.dropDown.defaultButton=this.bondSet.dropDown.buttonSet.buttons[this.bondSet.dropDown.buttonSet.buttons.length-1];
this.bondSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_bond_ambiguous_double",a.sketcher.iconPath,"AmbiguousDoubleBond","Ambiguous Double Bond",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_BOND;

a.sketcher.stateManager.STATE_NEW_BOND.bondOrder=2;
a.sketcher.stateManager.STATE_NEW_BOND.stereo=m.Bond.STEREO_AMBIGUOUS}))};
f.ToolbarManager.prototype.makeRingSet=function(a){this.ringSet=new d.ButtonSet(a.sketcher.id+"_buttons_ring");
this.buttonCyclohexane=new d.Button(a.sketcher.id+"_button_ring_cyclohexane",a.sketcher.iconPath,"Cyclohexane","Cyclohexane Ring",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_RING;
a.sketcher.stateManager.STATE_NEW_RING.numSides=
6;
a.sketcher.stateManager.STATE_NEW_RING.unsaturated=!1});
this.ringSet.buttons.push(this.buttonCyclohexane);
this.buttonBenzene=new d.Button(a.sketcher.id+"_button_ring_benzene",a.sketcher.iconPath,"Benzene","Benzene Ring",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_RING;
a.sketcher.stateManager.STATE_NEW_RING.numSides=6;
a.sketcher.stateManager.STATE_NEW_RING.unsaturated=!0});
this.ringSet.buttons.push(this.buttonBenzene);
this.buttonBond=new d.DummyButton(a.sketcher.id+
"_button_ring",a.sketcher.iconPath,"Cyclopentane","Other Ring");
this.ringSet.buttons.push(this.buttonBond);
this.ringSet.addDropDown("More Rings",a.sketcher.iconPath);
this.ringSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_ring_cyclopropane",a.sketcher.iconPath,"Cyclopropane","Cyclopropane Ring",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_RING;
a.sketcher.stateManager.STATE_NEW_RING.numSides=3;
a.sketcher.stateManager.STATE_NEW_RING.unsaturated=
!1}));
this.ringSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_ring_cyclobutane",a.sketcher.iconPath,"Cyclobutane","Cyclobutane Ring",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_RING;
a.sketcher.stateManager.STATE_NEW_RING.numSides=4;
a.sketcher.stateManager.STATE_NEW_RING.unsaturated=!1}));
this.ringSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_ring_cyclopentane",a.sketcher.iconPath,"Cyclopentane","Cyclopentane Ring",
function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_RING;
a.sketcher.stateManager.STATE_NEW_RING.numSides=5;
a.sketcher.stateManager.STATE_NEW_RING.unsaturated=!1}));
this.ringSet.dropDown.defaultButton=this.ringSet.dropDown.buttonSet.buttons[this.ringSet.dropDown.buttonSet.buttons.length-1];
this.ringSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_ring_cycloheptane",a.sketcher.iconPath,"Cycloheptane","Cycloheptane Ring",function(){a.sketcher.stateManager.currentState=
a.sketcher.stateManager.STATE_NEW_RING;
a.sketcher.stateManager.STATE_NEW_RING.numSides=7;
a.sketcher.stateManager.STATE_NEW_RING.unsaturated=!1}));
this.ringSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_ring_cyclooctane",a.sketcher.iconPath,"Cyclooctane","Cyclooctane Ring",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_NEW_RING;
a.sketcher.stateManager.STATE_NEW_RING.numSides=8;
a.sketcher.stateManager.STATE_NEW_RING.unsaturated=!1}))};
f.ToolbarManager.prototype.makeAttributeSet=
function(a){this.attributeSet=new d.ButtonSet(a.sketcher.id+"_buttons_attribute");
this.buttonChargePlus=new d.Button(a.sketcher.id+"_button_attribute_charge_increment",a.sketcher.iconPath,"IncreaseCharge","Increase Charge",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_CHARGE;
a.sketcher.stateManager.STATE_CHARGE.delta=1});
this.attributeSet.buttons.push(this.buttonChargePlus);
this.buttonAttribute=new d.DummyButton(a.sketcher.id+"_button_attribute",a.sketcher.iconPath,
"DecreaseCharge","Other Attribute");
this.attributeSet.buttons.push(this.buttonAttribute);
this.attributeSet.addDropDown("More Attributes",a.sketcher.iconPath);
this.attributeSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_attribute_charge_decrement",a.sketcher.iconPath,"DecreaseCharge","Decrease Charge",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_CHARGE;
a.sketcher.stateManager.STATE_CHARGE.delta=-1}));
this.attributeSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+
"_button_attribute_lonePair_increment",a.sketcher.iconPath,"AddLonePair","Add Lone Pair",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LONE_PAIR;
a.sketcher.stateManager.STATE_LONE_PAIR.delta=1}));
this.attributeSet.dropDown.buttonSet.buttons.push(new d.Button(a.sketcher.id+"_button_attribute_lonePair_decrement",a.sketcher.iconPath,"RemoveLonePair","Remove Lone Pair",function(){a.sketcher.stateManager.currentState=a.sketcher.stateManager.STATE_LONE_PAIR;
a.sketcher.stateManager.STATE_LONE_PAIR.delta=
-1}))}})(ChemDoodle,ChemDoodle.iChemLabs,ChemDoodle.io,ChemDoodle.sketcher.actions,ChemDoodle.sketcher.gui,ChemDoodle.sketcher.gui.desktop,ChemDoodle.structures,jQuery,document);

(function(a,e,g,b,f,d,m){a.SketcherCanvas=function(a,d,c,e,l,i){var h=this;
this.iconPath=e;
this.isMobile=l;
this.useServices=i;
this.id=a;
this.toolbarManager=new g.gui.ToolbarManager(this);
null!=e&&(this.toolbarManager.write(),f(m).load(function(){h.toolbarManager.setup()}),this.dialogManager=new g.gui.DialogManager(this));
this.stateManager=new g.states.StateManager(this);
this.historyManager=new g.actions.HistoryManager(this);
a&&this.create(a,d,c);
this.specs.atoms_circleDiameter_2D=7;
this.specs.atoms_circleBorderWidth_2D=0;
this.isHelp=!1;
this.helpPos=new b.Point(this.width-20,20);
this.clear();
this.lastPinchScale=1;
this.lastGestureRotate=0;
return!0};
a.SketcherCanvas.prototype=new a._Canvas;
a.SketcherCanvas.prototype.clear=function(){var a=new b.Molecule;
a.atoms[0]=new b.Atom("C",0,0,0);
this.loadMolecule(a)};
a.SketcherCanvas.prototype.drawSketcherDecorations=function(a){a.save();
a.translate(this.width/2,this.height/2);
a.rotate(this.specs.rotateAngle);
a.scale(this.specs.scale,this.specs.scale);
a.translate(-this.width/
2,-this.height/2);
null!=this.hovering&&this.hovering.drawDecorations(a);
null!=this.tempAtom&&(a.strokeStyle="#00FF00",a.fillStyle="#00FF00",a.lineWidth=1,a.beginPath(),a.moveTo(this.hovering.x,this.hovering.y),e.contextHashTo(a,this.hovering.x,this.hovering.y,this.tempAtom.x,this.tempAtom.y,2,2),a.stroke(),a.beginPath(),a.arc(this.tempAtom.x,this.tempAtom.y,3,0,2*d.PI,!1),a.fill(),this.tempAtom.isOverlap&&(a.strokeStyle="#C10000",a.lineWidth=1.2,a.beginPath(),a.arc(this.tempAtom.x,this.tempAtom.y,
7,0,2*d.PI,!1),a.stroke()));
if(null!=this.tempRing){a.strokeStyle="#00FF00";
a.fillStyle="#00FF00";
a.lineWidth=1;
a.beginPath();
if(this.hovering instanceof b.Atom){a.moveTo(this.hovering.x,this.hovering.y);
e.contextHashTo(a,this.hovering.x,this.hovering.y,this.tempRing[0].x,this.tempRing[0].y,2,2);
for(var f=1,c=this.tempRing.length;
f<c;
f++)e.contextHashTo(a,this.tempRing[f-1].x,this.tempRing[f-1].y,this.tempRing[f].x,this.tempRing[f].y,2,2);
e.contextHashTo(a,this.tempRing[this.tempRing.length-1].x,
this.tempRing[this.tempRing.length-1].y,this.hovering.x,this.hovering.y,2,2)}else if(this.hovering instanceof b.Bond){var f=this.hovering.a2,g=this.hovering.a1;
this.tempRing[0]==this.hovering.a1&&(f=this.hovering.a1,g=this.hovering.a2);
a.moveTo(f.x,f.y);
e.contextHashTo(a,f.x,f.y,this.tempRing[1].x,this.tempRing[1].y,2,2);
f=2;
for(c=this.tempRing.length;
f<c;
f++)e.contextHashTo(a,this.tempRing[f-1].x,this.tempRing[f-1].y,this.tempRing[f].x,this.tempRing[f].y,2,2);
e.contextHashTo(a,this.tempRing[this.tempRing.length-
1].x,this.tempRing[this.tempRing.length-1].y,g.x,g.y,2,2)}a.stroke();
f=0;
for(c=this.molecule.atoms.length;
f<c;
f++)this.molecule.atoms[f].isOverlap&&(a.strokeStyle="#C10000",a.lineWidth=1.2,a.beginPath(),a.arc(this.molecule.atoms[f].x,this.molecule.atoms[f].y,7,0,2*d.PI,!1),a.stroke())}a.restore()};
a.SketcherCanvas.prototype.drawChildExtras=function(a){this.drawSketcherDecorations(a);
if(!this.hideHelp){var b=a.createRadialGradient(this.width-20,20,10,this.width-20,20,2);
b.addColorStop(0,"#00680F");

b.addColorStop(1,"#FFFFFF");
//a.fillStyle=b;
//a.beginPath();
//a.arc(this.helpPos.x,this.helpPos.y,10,0,2*d.PI,!1);
//a.fill();
this.isHelp&&(a.lineWidth=2,a.strokeStyle="black",a.stroke());
a.fillStyle=this.isHelp?"red":"black";
a.textAlign="center";
a.textBaseline="middle";
a.font="14px sans-serif";
a.fillText("",this.helpPos.x,this.helpPos.y)}this.paidToHideTrademark||(b=a.measureText("ChemDoodle").width,a.textAlign="left",a.textBaseline="bottom",a.fillStyle="rgba(0, 90, 0, 0.5)",a.fillText("",this.width-
b-18,this.height-4),a.font="8px sans-serif",a.fillText("",this.width-18,this.height-12))};
a.SketcherCanvas.prototype.scaleEvent=function(a){a.op=new b.Point(a.p.x,a.p.y);
1!=this.specs.scale&&(a.p.x=this.width/2+(a.p.x-this.width/2)/this.specs.scale,a.p.y=this.height/2+(a.p.y-this.height/2)/this.specs.scale)};
a.SketcherCanvas.prototype.checkScale=function(){0.5>this.specs.scale?this.specs.scale=0.5:10<this.specs.scale&&(this.specs.scale=10)};
a.SketcherCanvas.prototype.click=function(a){this.scaleEvent(a);

this.stateManager.currentState.click(a)};
a.SketcherCanvas.prototype.rightclick=function(a){this.scaleEvent(a);
this.stateManager.currentState.rightclick(a)};
a.SketcherCanvas.prototype.dblclick=function(a){this.scaleEvent(a);
this.stateManager.currentState.dblclick(a)};
a.SketcherCanvas.prototype.mousedown=function(a){this.scaleEvent(a);
this.stateManager.currentState.mousedown(a)};
a.SketcherCanvas.prototype.rightmousedown=function(a){this.scaleEvent(a);
this.stateManager.currentState.rightmousedown(a)};

a.SketcherCanvas.prototype.mousemove=function(a){this.isHelp=!1;
10>a.p.distance(this.helpPos)&&(this.isHelp=!0);
this.scaleEvent(a);
this.stateManager.currentState.mousemove(a)};
a.SketcherCanvas.prototype.mouseout=function(a){this.scaleEvent(a);
this.stateManager.currentState.mouseout(a)};
a.SketcherCanvas.prototype.mouseover=function(a){this.scaleEvent(a);
this.stateManager.currentState.mouseover(a)};
a.SketcherCanvas.prototype.mouseup=function(a){this.scaleEvent(a);
this.stateManager.currentState.mouseup(a)};

a.SketcherCanvas.prototype.rightmouseup=function(a){this.scaleEvent(a);
this.stateManager.currentState.rightmouseup(a)};
a.SketcherCanvas.prototype.mousewheel=function(a,b){this.scaleEvent(a);
this.stateManager.currentState.mousewheel(a,b)};
a.SketcherCanvas.prototype.drag=function(a){this.scaleEvent(a);
this.stateManager.currentState.drag(a)};
a.SketcherCanvas.prototype.keydown=function(a){this.scaleEvent(a);
this.stateManager.currentState.keydown(a)};
a.SketcherCanvas.prototype.keypress=function(a){this.scaleEvent(a);

this.stateManager.currentState.keypress(a)};
a.SketcherCanvas.prototype.keyup=function(a){this.scaleEvent(a);
this.stateManager.currentState.keyup(a)};
a.SketcherCanvas.prototype.touchstart=function(a){if(a.originalEvent.touches&&1<a.originalEvent.touches.length){if(null!=this.tempAtom||null!=this.tempRing)this.hovering=this.tempRing=this.tempAtom=null,this.repaint();
this.lastPoint=null}else this.scaleEvent(a),this.stateManager.currentState.mousemove(a),this.stateManager.currentState.mousedown(a)};
a.SketcherCanvas.prototype.touchmove=
function(a){this.scaleEvent(a);
this.stateManager.currentState.drag(a)};
a.SketcherCanvas.prototype.touchend=function(a){this.scaleEvent(a);
this.stateManager.currentState.mouseup(a);
null!=this.hovering&&(this.stateManager.currentState.clearHover(),this.repaint())};
a.SketcherCanvas.prototype.gesturechange=function(a){1!=a.originalEvent.scale-this.lastPinchScale&&(this.specs.scale*=a.originalEvent.scale/this.lastPinchScale,this.checkScale(),this.lastPinchScale=a.originalEvent.scale);
if(0!=this.lastGestureRotate-
a.originalEvent.rotation){var e=(this.lastGestureRotate-a.originalEvent.rotation)/180*d.PI,c=new b.Point(this.width/2,this.height/2);
if(null==this.parentAction)this.parentAction=new g.actions.RotateAction(this.molecule.atoms,e,c),this.historyManager.pushUndo(this.parentAction);
else{this.parentAction.dif+=e;
for(var f=0,l=this.molecule.atoms.length;
f<l;
f++){var i=c.distance(this.molecule.atoms[f]),h=c.angle(this.molecule.atoms[f])+e;
this.molecule.atoms[f].x=c.x+i*d.cos(h);
this.molecule.atoms[f].y=c.y-
i*d.sin(h)}this.molecule.check()}this.lastGestureRotate=a.originalEvent.rotation}this.repaint()};
a.SketcherCanvas.prototype.gestureend=function(){this.lastPinchScale=1;
this.lastGestureRotate=0;
this.parentAction=null}})(ChemDoodle,ChemDoodle.extensions,ChemDoodle.sketcher,ChemDoodle.structures,jQuery,Math,window);

