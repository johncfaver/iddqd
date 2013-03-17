#!/usr/bin/python

import cgi, os, cgitb, base64, psycopg2, subprocess,sys
from urllib import unquote_plus
cgitb.enable()

#MOVE TO UPLOAD DIRECTORY
os.chdir('../uploads/')
##############OPTIONS####################
debug=False
maxdata=5
#########################################

#####DATATYPES############
class bindingdata:
	def __init__(self):
		self.datatypeid=0
		self.targetid=0
		self.value=0
		self.notes=0
		self.notesid=0
		self.moldataid=0
class propertydata:
	def __init__(self):
		self.datatypeid=0
		self.value=0
		self.notes=0	
		self.notesid=0
		self.moldataid=0
class docdata:
	def __init__(self):
		self.datatypeid=0
		self.obj=0
		self.filename=0
		self.notes=0
		self.notesid=0
		self.moldataid=0
#############Collect field data##########
form=cgi.FieldStorage()
keys=form.keys()
moltext=form['moltext'].value.replace('\r','').split('\n')
molfig64=form['molfig'].value.split(',')[1]

if debug:
	print 'Content-type: text/html\n\n'
	print 'Received the following:\n<br />'
	for i in keys:
		print i+'='+form[i].value+'<br />'
	sys.exit()	

if 'molname' in keys:
	molname=form['molname'].value
else:
	molname=0
if 'iupacname' in keys:
	iupacname=form['iupacname'].value
else:
	iupacname=0
if 'cas' in keys:
	cas=form['cas'].value
else:
	cas=0
if 'molid' in keys:
	molid=int(form['molid'].value)
else:
	molid=-1
if 'userid' in keys:
	authorid=form['userid'].value
else:
	authorid=0
if 'username' in keys:
	author=unquote_plus(form['username'].value)
else:
	author=0
if 'oldcommentids' in keys:
	oldcommentids=form['oldcommentids'].value.split(',')[:-1]
else:
	oldcommentids=0
if 'oldbindingdataids' in keys:
	oldbindingdataids=form['oldbindingdataids'].value.split(',')[:-1]
else:
	oldbindingdataids=0
if 'oldpropertydataids' in keys:
	oldpropertydataids=form['oldpropertydataids'].value.split(',')[:-1]
else:
	oldpropertydataids=0
if 'olddocdataids' in keys:
	olddocdataids=form['olddocdataids'].value.split(',')[:-1]
else:
	olddocdataids=0


bindingdatas=[]
propertydatas=[]
commentdatas=[]
docdatas=[]
#LOAD OLD DATAS 
for i in oldbindingdataids:
	if 'bindingdata_datatypeid_'+i in keys and 'bindingdata_value_'+i in keys and 'bindingdata_targetid_'+i in keys:	
		if(not form['bindingdata_value_'+i].value):	
			continue
		bindingdatas.append(bindingdata())
		bindingdatas[-1].datatypeid=form['bindingdata_datatypeid_'+i].value
		bindingdatas[-1].targetid=form['bindingdata_targetid_'+i].value
		try:
			bindingdatas[-1].value=eval(form['bindingdata_value_'+i].value)
		except:
			bindingdatas[-1].value=0
		bindingdatas[-1].moldataid=i
		if 'textarea_bindingdata_notes_'+i in keys:
			bindingdatas[-1].notes=form['textarea_bindingdata_notes_'+i].value
			bindingdatas[-1].notesid=form['bindingdata_notesid_'+i].value
for i in oldpropertydataids:
	if 'propertydata_datatypeid_'+i in keys and 'propertydata_value_'+i in keys:	
		if(not form['propertydata_value_'+i].value):
			continue
		propertydatas.append(propertydata())
		propertydatas[-1].datatypeid=form['propertydata_datatypeid_'+i].value
		try:
			propertydatas[-1].value=eval(form['propertydata_value_'+i].value)
		except:
			propertydatas[-1].value=0
		propertydatas[-1].moldataid=i
		if 'textarea_propertydata_notes_'+i in keys:
			propertydatas[-1].notes=form['textarea_propertydata_notes_'+i].value
			propertydatas[-1].notesid=form['propertydata_notesid_'+i].value
for i in olddocdataids:
	if 'docdata_datatypeid_'+i in keys and 'docdata_filename_'+i in keys:	
		docdatas.append(docdata())
		docdatas[-1].datatypeid=form['docdata_datatypeid_'+i].value
		docdatas[-1].obj=0
		docdatas[-1].filename=form['docdata_filename_'+i].value
		docdatas[-1].moldataid=i
		if 'textarea_docdata_notes_'+i in keys:
			docdatas[-1].notes=form['textarea_docdata_notes_'+i].value
			docdatas[-1].notesid=form['docdata_notesid_'+i].value
#LOAD NEW DATAS
for i in xrange(1,maxdata+1):
	if 'bindingdata_datatypeid_new_'+str(i) in keys and 'bindingdata_value_new_'+str(i) in keys and 'bindingdata_targetid_new_'+str(i) in keys:	
		if(not form['bindingdata_value_new_'+str(i)].value):	
			continue
		bindingdatas.append(bindingdata())
		bindingdatas[-1].datatypeid=form['bindingdata_datatypeid_new_'+str(i)].value
		bindingdatas[-1].targetid=form['bindingdata_targetid_new_'+str(i)].value
		try:
			bindingdatas[-1].value=eval(form['bindingdata_value_new_'+str(i)].value)
		except:
			bindingdatas[-1].value=0
		if 'textarea_bindingdata_notes_new_'+str(i) in keys and form['textarea_bindingdata_notes_new_'+str(i)].value!='':
			bindingdatas[-1].notes=form['textarea_bindingdata_notes_new_'+str(i)].value
	if 'propertydata_datatypeid_new_'+str(i) in keys and 'propertydata_value_new_'+str(i) in keys:	
		if(not form['propertydata_value_new_'+str(i)].value):
			continue
		propertydatas.append(propertydata())
		propertydatas[-1].datatypeid=form['propertydata_datatypeid_new_'+str(i)].value
		try:
			propertydatas[-1].value=eval(form['propertydata_value_new_'+str(i)].value)
		except:
			propertydatas[-1].value=0
		if 'textarea_propertydata_notes_new_'+str(i) in keys and form['textarea_propertydata_notes_new_'+str(i)].value!='':
			propertydatas[-1].notes=form['textarea_propertydata_notes_new_'+str(i)].value
	if 'docdata_value_new_'+str(i) in keys and form['docdata_value_new_'+str(i)].filename:	
		docdatas.append(docdata())
		docdatas[-1].datatypeid=form['docdata_datatypeid_new_'+str(i)].value
		docdatas[-1].obj=form['docdata_value_new_'+str(i)]
		docdatas[-1].filename=form['docdata_value_new_'+str(i)].filename
		if 'textarea_docdata_notes_new_'+str(i) in keys and form['textarea_docdata_notes_new_'+str(i)].value!='':
			docdatas[-1].notes=form['textarea_docdata_notes_new_'+str(i)].value

#########################################


dbconn = psycopg2.connect("dbname=iddqddb user=iddqd password=loblaw")
q = dbconn.cursor()

###UPDATE MOLECULE TABLE###########
query='UPDATE molecules SET molname=%s'
if(iupacname):
	query+=', iupac=%s'
if(cas):
	query+=', cas=%s'
query+=' where molid=%s'
options=[molname]
if(iupacname):
	options.append(iupacname)
if(cas):
	options.append(cas)
options.append(molid)
q.execute(query,options)
##############################

######UPDATE OLD DATA#####################
for i in xrange(len(oldbindingdataids)):
	query='UPDATE moldata SET datatype=%s, targetid=%s, value=%s, dateadded=localtimestamp, authorid=%s where moldataid=%s'
	options=[bindingdatas[i].datatypeid,bindingdatas[i].targetid,bindingdatas[i].value,authorid,bindingdatas[i].moldataid]
	q.execute(query,options)
	if(bindingdatas[i].notesid!='0'):
		query='UPDATE datacomments SET dataid=%s,authorid=%s,dateadded=localtimestamp,datacomment=%s where datacommentid=%s'
		options=[bindingdatas[i].moldataid,authorid,bindingdatas[i].notes,bindingdatas[i].notesid]
		q.execute(query,options)
	elif(bindingdatas[i].notes):
		query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s) '	
		options=[bindingdatas[i].moldataid,authorid,bindingdatas[i].notes]
		q.execute(query,options)
for i in xrange(len(oldpropertydataids)):
	query='UPDATE moldata SET datatype=%s, targetid=null, value=%s, dateadded=localtimestamp, authorid=%s where moldataid=%s'
	options=[propertydatas[i].datatypeid,propertydatas[i].value,authorid,propertydatas[i].moldataid]
	q.execute(query,options)
	if(propertydatas[i].notesid!='0'):
		query='UPDATE datacomments SET dataid=%s,authorid=%s,dateadded=localtimestamp,datacomment=%s where datacommentid=%s'
		options=[propertydatas[i].moldataid,authorid,propertydatas[i].notes,propertydatas[i].notesid]
		q.execute(query,options)
	elif(propertydatas[i].notes):
		query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s) '	
		options=[propertydatas[i].moldataid,authorid,propertydatas[i].notes]
		q.execute(query,options)
for i in xrange(len(olddocdataids)):
	if(docdatas[i].notesid!='0'):
		query='UPDATE datacomments SET dataid=%s,authorid=%s,dateadded=localtimestamp,datacomment=%s where datacommentid=%s'
		options=[docdatas[i].moldataid,authorid,docdatas[i].notes,docdatas[i].notesid]
		q.execute(query,options)
	elif(docdatas[i].notes):
		query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s) '	
		options=[docdatas[i].moldataid,authorid,docdatas[i].notes]
		q.execute(query,options)
########################################

######INSERT NEW DATA####################
for i in xrange(len(oldbindingdataids),len(bindingdatas)):
	query='INSERT INTO moldata (molid,authorid,dateadded,targetid,datatype,value)'
	query+=' VALUES (%s, %s, localtimestamp, %s, %s, %s) RETURNING moldataid '
	options=[molid,authorid,bindingdatas[i].targetid,bindingdatas[i].datatypeid,bindingdatas[i].value]
	q.execute(query,options)
	if(bindingdatas[i].notes):
		dataid=q.fetchone()[0]
		query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s) '	
		options=[dataid,authorid,bindingdatas[i].notes]
		q.execute(query,options)
for i in xrange(len(oldpropertydataids),len(propertydatas)):
	query='INSERT INTO moldata (molid,authorid,dateadded,datatype,value)'
	query+=' VALUES (%s, %s, localtimestamp, %s, %s) RETURNING moldataid'
	options=[molid,authorid,propertydatas[i].datatypeid,propertydatas[i].value]
	q.execute(query,options)
	if(propertydatas[i].notes):
		dataid=q.fetchone()[0]
		query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s)'
		options=[dataid,authorid,propertydatas[i].notes]
		q.execute(query,options)
for i in xrange(len(olddocdataids),len(docdatas)):
	query='INSERT INTO moldata (molid,authorid,dateadded,datatype)'
	query+=' VALUES (%s, %s, localtimestamp, %s) RETURNING moldataid'
	options=[molid,authorid,docdatas[i].datatypeid]
	q.execute(query,options)
	docdatas[i].moldataid=q.fetchone()[0]
	if(docdatas[i].notes):
		query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s)'
		options=[docdatas[i].moldataid,authorid,docdatas[i].notes]
		q.execute(query,options)
############################

dbconn.commit()
q.close()
dbconn.close()


#############FILE HANDLING - REPLACE STRUCTURE FILES/ WRITE NEW DOCUMENTS###############
recalculate=False
with open('/tmp/'+str(molid)+'.mol','w') as f:
	f.write(molname+' \n')
	for line in moltext[1:]:
		f.write(line+' \n')
if os.path.isfile('structures/'+str(molid)+'.mol'):
	moldif=subprocess.call(['/usr/bin/diff', '/tmp/'+str(molid)+'.mol','structures/'+str(molid)+'.mol'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
	if moldif==0:	
		subprocess.call(['/bin/rm','/tmp/'+str(molid)+'.mol'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
	else:
		subprocess.call(['/bin/mv','/tmp/'+str(molid)+'.mol', 'structures/'+str(molid)+'.mol'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))	
		recalculate=True

with open('sketches/'+str(molid)+'.png','w') as f:
	f.write(base64.decodestring(molfig64))
for i in xrange(len(olddocdataids),len(docdatas)):
	with open('documents/'+str(molid)+'_'+str(docdatas[i].datatypeid)+'_'+str(docdatas[i].moldataid)+'_'+str(docdatas[i].filename),'w') as f:
		while 1:
			chunk=docdatas[i].obj.file.read(100000)
			if not chunk:
				break
			f.write(chunk)
##########################################


#############COMPUTATION##################
subprocess.call(['/usr/bin/convert','sketches/'+str(molid)+'.png','-trim','sketches/'+str(molid)+'.jpg'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
if(recalculate):
	subprocess.Popen([sys.executable,'../cgi-bin/computations.py',str(molid)],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
############################################

print 'Location: ../viewmolecule.php?molid='+str(molid)
print ''
