#!/usr/bin/env python

# editmol.py
#
# Update given molecule based on input from editmolecule.php page.
# Check if structure changed before doing any computations or rendering
# Send to either pngwriter or viewmolecule depending on the presence
# of a structure change.
#

import os 
import sys
import cgi, cgitb
import base64
import subprocess
import filecmp
import shutil
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

try:
    moltext=form['moltext'].value.replace('\r','').split('\n')
    molfig64=form['molfig'].value.split(',')[1]
except Exception: 
    config.returnhome(63)
    sys.exit()

if debug:
    print 'Content-type: text/html\n\n'
    print 'Received the following:\n<br />'
    for i in keys:
        print i+'='+form[i].value+'<br />'
    sys.exit()    

if 'molname' in keys:
    molname=form['molname'].value.strip().replace(' ','_')
else:
    molname=''
if 'iupacname' in keys:
    iupacname=form['iupacname'].value.strip()
else:
    iupacname=''
if 'cas' in keys:
    cas=form['cas'].value.strip()
else:
    cas=''
if 'molid' in keys:
    molid=int(form['molid'].value)
else:
    molid=0
if 'userid' in keys:
    userid=form['userid'].value
else:
    userid=0
if 'token' in keys:
    token=form['token'].value
else:
    token=''
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

##Check inputs - empty molname not valid
if(not molname.strip()):
    print 'Location: ../editmolecule.php?emptyname=1&molid='+str(molid)+' \n\n'
    sys.exit()
#Must be logged in with valid token.
if(not userid or not token or not molid):
    config.returnhome(30)
    sys.exit()

bindingdatas=[]
propertydatas=[]
commentdatas=[]
docdatas=[]

#LOAD OLD DATA INTO LIST
for i in oldbindingdataids:
    if 'bindingdata_datatypeid_'+i in keys and 'bindingdata_value_'+i in keys and 'bindingdata_targetid_'+i in keys:    
        if(not form['bindingdata_value_'+i].value):    
            continue
        bindingdatas.append(bindingdata())
        bindingdatas[-1].datatypeid=form['bindingdata_datatypeid_'+i].value
        bindingdatas[-1].targetid=form['bindingdata_targetid_'+i].value
        try:
            bindingdatas[-1].value=float(form['bindingdata_value_'+i].value)
        except Exception:
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
            propertydatas[-1].value=float(form['propertydata_value_'+i].value)
        except Exception:
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

#LOAD NEW DATA
for i in xrange(1,maxdata+1):
    if 'bindingdata_datatypeid_new_'+str(i) in keys and 'bindingdata_value_new_'+str(i) in keys and 'bindingdata_targetid_new_'+str(i) in keys:    
        if(not form['bindingdata_value_new_'+str(i)].value):    
            continue
        bindingdatas.append(bindingdata())
        bindingdatas[-1].datatypeid=form['bindingdata_datatypeid_new_'+str(i)].value
        bindingdatas[-1].targetid=form['bindingdata_targetid_new_'+str(i)].value
        try:
            bindingdatas[-1].value=float(form['bindingdata_value_new_'+str(i)].value)
        except Exception:
            bindingdatas[-1].value=0
        if 'textarea_bindingdata_notes_new_'+str(i) in keys and form['textarea_bindingdata_notes_new_'+str(i)].value!='':
            bindingdatas[-1].notes=form['textarea_bindingdata_notes_new_'+str(i)].value
    if 'propertydata_datatypeid_new_'+str(i) in keys and 'propertydata_value_new_'+str(i) in keys:    
        if(not form['propertydata_value_new_'+str(i)].value):
            continue
        propertydatas.append(propertydata())
        propertydatas[-1].datatypeid=form['propertydata_datatypeid_new_'+str(i)].value
        try:
            propertydatas[-1].value=float(form['propertydata_value_new_'+str(i)].value)
        except Exception:
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

try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
except Exception:
    config.returnhome(68)
    sys.exit()

#Check for token.
q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
dbtoken = q.fetchone()[0]
if(dbtoken != token):
    config.returnhome(70)
    sys.exit()

###UPDATE MOLECULE TABLE########### Only original author can edit molname,iupac,cas
query='UPDATE molecules SET molname=%s, iupac=%s, cas=%s WHERE molid=%s AND authorid=%s'
options=[molname,iupacname,cas,molid,userid]
q.execute(query,options)
if(q.rowcount==1):
    userisauthor=True
else:
    userisauthor=False
##############################

######UPDATE OLD DATA#####################
#Must be author of data to edit data. Timestamp is updated when editing.
for i in xrange(len(oldbindingdataids)):
    query='UPDATE moldata SET datatype=%s, targetid=%s, value=%s, dateadded=localtimestamp WHERE authorid=%s AND moldataid=%s'
    options=[bindingdatas[i].datatypeid,bindingdatas[i].targetid,bindingdatas[i].value,userid,bindingdatas[i].moldataid]
    q.execute(query,options)
    if(bindingdatas[i].notesid!='0'):
        query='UPDATE datacomments SET dataid=%s,dateadded=localtimestamp,datacomment=%s where datacommentid=%s and authorid=%s'
        options=[bindingdatas[i].moldataid,bindingdatas[i].notes,bindingdatas[i].notesid,userid]
        q.execute(query,options)
    #elif(bindingdatas[i].notes):  #ALLOWS OTHERS TO ADD NEW NOTES ON A USERS DATA. CURRENTLY DISALLOWED.
    #    query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s) '    
    #    options=[bindingdatas[i].moldataid,userid,bindingdatas[i].notes]
    #    q.execute(query,options)
for i in xrange(len(oldpropertydataids)):
    query='UPDATE moldata SET datatype=%s, targetid=null, value=%s, dateadded=localtimestamp where authorid=%s AND moldataid=%s'
    options=[propertydatas[i].datatypeid,propertydatas[i].value,userid,propertydatas[i].moldataid]
    q.execute(query,options)
    if(propertydatas[i].notesid!='0'):
        query='UPDATE datacomments SET dataid=%s,dateadded=localtimestamp,datacomment=%s where authorid=%s AND datacommentid=%s'
        options=[propertydatas[i].moldataid,propertydatas[i].notes,userid,propertydatas[i].notesid]
        q.execute(query,options)
    #elif(propertydatas[i].notes): #ALLOWS OTHERS TO ADD NEW NOTES ON A USERS DATA. CURRENTLY DISALLOWED.
    #    query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s) '    
    #    options=[propertydatas[i].moldataid,userid,propertydatas[i].notes]
    #    q.execute(query,options)
for i in xrange(len(olddocdataids)):
    if(docdatas[i].notesid!='0'):
        query='UPDATE datacomments SET dataid=%s,dateadded=localtimestamp,datacomment=%s where authorid=%s AND datacommentid=%s'
        options=[docdatas[i].moldataid,docdatas[i].notes,userid,docdatas[i].notesid]
        q.execute(query,options)
    #elif(docdatas[i].notes):     #ALLOWS OTHERS TO ADD NEW NOTES ON A USERS DATA. CURRENTLY DISALLOWED.
    #    query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s) '    
    #    options=[docdatas[i].moldataid,userid,docdatas[i].notes]
    #    q.execute(query,options)
########################################

######INSERT NEW DATA####################
for i in xrange(len(oldbindingdataids),len(bindingdatas)):
    query='INSERT INTO moldata (molid,authorid,dateadded,targetid,datatype,value)'
    query+=' VALUES (%s, %s, localtimestamp, %s, %s, %s) RETURNING moldataid '
    options=[molid,userid,bindingdatas[i].targetid,bindingdatas[i].datatypeid,bindingdatas[i].value]
    q.execute(query,options)
    if(bindingdatas[i].notes):
        dataid=q.fetchone()[0]
        query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s) '    
        options=[dataid,userid,bindingdatas[i].notes]
        q.execute(query,options)
for i in xrange(len(oldpropertydataids),len(propertydatas)):
    query='INSERT INTO moldata (molid,authorid,dateadded,datatype,value)'
    query+=' VALUES (%s, %s, localtimestamp, %s, %s) RETURNING moldataid'
    options=[molid,userid,propertydatas[i].datatypeid,propertydatas[i].value]
    q.execute(query,options)
    if(propertydatas[i].notes):
        dataid=q.fetchone()[0]
        query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s)'
        options=[dataid,userid,propertydatas[i].notes]
        q.execute(query,options)
for i in xrange(len(olddocdataids),len(docdatas)):
    query='INSERT INTO moldata (molid,authorid,dateadded,datatype)'
    query+=' VALUES (%s, %s, localtimestamp, %s) RETURNING moldataid'
    options=[molid,userid,docdatas[i].datatypeid]
    q.execute(query,options)
    docdatas[i].moldataid=q.fetchone()[0]
    if(docdatas[i].notes):
        query='INSERT INTO datacomments (dataid,authorid,dateadded,datacomment) values (%s,%s,localtimestamp,%s)'
        options=[docdatas[i].moldataid,userid,docdatas[i].notes]
        q.execute(query,options)
############################

dbconn.commit()
q.close()
dbconn.close()


#############FILE HANDLING - REPLACE STRUCTURE FILES/ WRITE NEW DOCUMENTS###############
#Only the original author can edit a structure.
#Write a new temporary mol file. See if it differs from the one we have stored.
#If it differs, then we need to redo calculations and image rendering.
recalculate=False
if(userisauthor):
    with open('/tmp/'+str(molid)+'.mol','w') as f:
        f.write(molname+' \n')
        for line in moltext[1:]:
            f.write(line+' \n')
    if os.path.isfile('structures/'+str(molid)+'.mol'):
        samefile = filecmp.cmp('/tmp/'+str(molid)+'.mol','structures/'+str(molid)+'.mol')
        if not samefile:    
            os.remove('structures/'+str(molid)+'.mol')
            shutil.move('/tmp/'+str(molid)+'.mol', 'structures/'+str(molid)+'.mol')
            recalculate=True
        else:
            os.remove('/tmp/'+str(molid)+'.mol')

#Store any newly uploaded documents.
for i in xrange(len(olddocdataids),len(docdatas)):
    with open('documents/'+str(molid)+'_'+str(docdatas[i].datatypeid)+'_'+str(docdatas[i].moldataid)+'_'+str(docdatas[i].filename),'w') as f:
        while 1:
            chunk=docdatas[i].obj.file.read(100000)
            if not chunk:
                break
            f.write(chunk)
##########################################


#############COMPUTATION##################
if(recalculate):
    os.chdir('../../cgi-bin')
    subprocess.Popen([sys.executable,'computations.py',str(molid),molname],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))

###############REDIRECT####################

if(recalculate):
    print 'Location: ../pngwriter.php?molid='+str(molid)+'&dest=vm \n\n'
else:
    print 'Location: ../viewmolecule.php?molid='+str(molid)+' \n\n'
