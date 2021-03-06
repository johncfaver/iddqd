#!/usr/bin/env python

#
# Inserts new molecule data into database.
#
import os
import sys
import cgi, cgitb
import base64
import subprocess 
import psycopg2
import config

cgitb.enable(display=0,logdir="../log/",format="text")

#MOVE TO UPLOAD DIRECTORY
os.chdir('../public/uploads/')
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
class propertydata:
    def __init__(self):
        self.datatypeid=0
        self.value=0
        self.notes=0    
class docdata:
    def __init__(self):
        self.datatypeid=0
        self.obj=0
        self.filename=0
        self.notes=0
        self.moldataid=0
#############Collect field data##########
form=cgi.FieldStorage()
keys=form.keys()

if debug: #print received variables
    print 'Content-type: text/html\n\n'
    print 'Received the following:\n<br />'
    for i in keys:
        print i+'='+form[i].value+'<br />'
    sys.exit()    

try:
    moltext=form['moltext'].value.replace('\r','').split('\n')
    molfig64=form['molfig'].value.split(',')[1]
except Exception:
    config.returnhome(62)
    sys.exit()

if 'molname' in keys:
    molname=form['molname'].value.strip().replace(' ','_')
else:
    molname=0
if 'iupacname' in keys:
    iupacname=form['iupacname'].value.strip()
else:
    iupacname=0
if 'cas' in keys:
    cas=form['cas'].value.strip()
else:
    cas=0
if 'molnotes' in keys:
    molnotes=form['molnotes'].value
else:
    molnotes=0
if 'userid' in keys:
    authorid=form['userid'].value
else:
    authorid=0
if 'token' in keys:
    token = form['token'].value
else:
    token = ''

#CHECK INPUTS
#molname cannot be empty
if(not molname.strip()):
    print 'Location: ../addmolecule.php?emptyname=1 \n\n'
    sys.exit()
if(not authorid or not token): #This should never happen
    config.returnhome(39)
    sys.exit()

#COLLECT INPUT DATA
bindingdatas=[]
propertydatas=[]
docdatas=[]
for i in xrange(maxdata):
    if 'bindingdata_value_new_'+str(i+1) in keys and form['bindingdata_value_new_'+str(i+1)].value!='':
        bindingdatas.append(bindingdata())
        bindingdatas[i].datatypeid=form['bindingdata_datatypeid_new_'+str(i+1)].value
        bindingdatas[i].targetid=form['bindingdata_targetid_new_'+str(i+1)].value
        try:
            bindingdatas[i].value=float(form['bindingdata_value_new_'+str(i+1)].value)
        except Exception:
            bindingdatas[i].value=0
        if 'textarea_bindingdata_notes_new_'+str(i+1) in keys and form['textarea_bindingdata_notes_new_'+str(i+1)].value!='':
            bindingdatas[i].notes=form['textarea_bindingdata_notes_new_'+str(i+1)].value
    if 'propertydata_value_new_'+str(i+1) in keys and form['propertydata_value_new_'+str(i+1)].value!='':    
        propertydatas.append(propertydata())
        propertydatas[i].datatypeid=form['propertydata_datatypeid_new_'+str(i+1)].value
        try:
            propertydatas[i].value=float(form['propertydata_value_new_'+str(i+1)].value)
        except Exception:
            propertydatas[i].value=0
        if 'textarea_propertydata_notes_new_'+str(i+1) in keys and form['textarea_propertydata_notes_new_'+str(i+1)].value!='':
            propertydatas[i].notes=form['textarea_propertydata_notes_new_'+str(i+1)].value
    if 'docdata_value_new_'+str(i+1) in keys and form['docdata_value_new_'+str(i+1)].filename:
        docdatas.append(docdata())
        docdatas[i].datatypeid=form['docdata_datatypeid_new_'+str(i+1)].value
        docdatas[i].obj=form['docdata_value_new_'+str(i+1)]
        docdatas[i].filename=form['docdata_value_new_'+str(i+1)].filename
        if 'textarea_docdata_notes_new_'+str(i+1) in keys and form['textarea_docdata_notes_new_'+str(i+1)].value!='':
            docdatas[i].notes=form['textarea_docdata_notes_new_'+str(i+1)].value
#########################################


dbconn = psycopg2.connect(config.dsn)
q = dbconn.cursor()

#CHECK USER TOKEN
q.execute('SELECT token FROM tokens WHERE userid=%s',[authorid])
dbtoken = q.fetchone()[0]
if(dbtoken != token):
    config.returnhome(49)
    sys.exit()

#CHECK IF MOLNAME EXISTS
q.execute('SELECT molid FROM molecules WHERE molname=%s',[molname]) 
r=q.fetchall()
if len(r)>0:
    existingmolid=r[0][0]
    q.close()
    dbconn.close()
    print 'Location: ../viewmolecule.php?molid='+str(existingmolid)
    print 
    sys.exit()


###ADD TO MOLECULE AND MOLCOMMENT TABLES###########
query='INSERT INTO molecules (molname,authorid,dateadded'
if(cas):
    query+=',cas'
if(iupacname):
    query+=',iupac'
query+=') VALUES(%s, %s, localtimestamp'
for i in filter(None,[cas,iupacname]):
    query+=',%s'
query+=') RETURNING molid'
options=[molname,authorid]
for i in filter(None,[cas,iupacname]):
    options.append(i)
q.execute(query,options)
molid=q.fetchone()[0]
if(molnotes):
    query='INSERT INTO molcomments (molid,molcomment,authorid,dateadded) values(%s,%s,%s,localtimestamp)'
    options=[molid,molnotes,authorid]
    q.execute(query,options)
##############################


######ADD TO DATA TABLE########### THERE ARE UP TO (maxdata) VALUES EACH OF PROPERTYDATA BINDNGDATA AND DOCDATA TYPES
for i in xrange(len(bindingdatas)):
    query='INSERT INTO moldata (molid,authorid,dateadded,targetid,datatype,value)'
    query+=' VALUES (%s, %s, localtimestamp, %s, %s, %s) RETURNING moldataid '
    options=[molid,authorid,bindingdatas[i].targetid,bindingdatas[i].datatypeid,bindingdatas[i].value]
    q.execute(query,options)
    if(bindingdatas[i].notes):
        dataid=q.fetchone()[0]
        query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s) '    
        options=[dataid,authorid,bindingdatas[i].notes]
        q.execute(query,options)
for i in xrange(len(propertydatas)):
    query='INSERT INTO moldata (molid,authorid,dateadded,datatype,value)'
    query+=' VALUES (%s, %s, localtimestamp, %s, %s) RETURNING moldataid'
    options=[molid,authorid,propertydatas[i].datatypeid,propertydatas[i].value]
    q.execute(query,options)
    if(propertydatas[i].notes):
        dataid=q.fetchone()[0]
        query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values(%s,%s,localtimestamp,%s)'
        options=[dataid,authorid,propertydatas[i].notes]
        q.execute(query,options)
for i in xrange(len(docdatas)):
    query='INSERT INTO moldata (molid,authorid,dateadded,datatype)'
    query+=' VALUES (%s, %s, localtimestamp, %s) RETURNING moldataid'
    options=[molid,authorid,docdatas[i].datatypeid]
    q.execute(query,options)
    docdatas[i].moldataid=q.fetchone()[0]
    if(docdatas[i].notes):
        query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values(%s,%s,localtimestamp,%s)'
        options=[docdatas[i].moldataid,authorid,docdatas[i].notes]
        q.execute(query,options)
############################

dbconn.commit()
q.close()
dbconn.close()

#############FILE HANDLING###############
with open('structures/'+str(molid)+'.mol','w') as f:
    f.write(molname+' \n')
    for line in moltext[1:]:
        f.write(line+' \n')
for i in xrange(len(docdatas)):
    with open('documents/'+str(molid)+'_'+str(docdatas[i].datatypeid)+'_'+str(docdatas[i].moldataid)+'_'+str(docdatas[i].filename),'w') as f:
        while 1:
            chunk=docdatas[i].obj.file.read(100000)
            if not chunk:
                break
            f.write(chunk)
##########################################

#############COMPUTATION##################
os.chdir('../../cgi-bin')
subprocess.Popen([sys.executable,'computations.py',str(molid),molname],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
############################################

print 'Location: ../pngwriter.php?molid='+str(molid)+'&dest=am \n\n'
