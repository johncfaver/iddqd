#!/usr/bin/env python

#
# Edits target in database.
<<<<<<< HEAD
=======
# Only target author can edit target information.
# Anyone can upload documents and edit their own documents.
>>>>>>> iddqd-dev/master
# Returns user to target page.
#
import cgi, cgitb, psycopg2, sys
cgitb.enable(display=0,logdir="../log/",format="text")
import config
<<<<<<< HEAD
=======
#Only 5 documents can be uploaded at a time
maxdata = 5
debug=False

#Using class structure for document data to match what is done in editmol.py
#It may be better to use dict instead? 
class docdata:
    def __init__(self):
        self.datatype=0
        self.obj=0
        self.filename=0
        self.notes=''
        self.targetdataid=0
>>>>>>> iddqd-dev/master

form=cgi.FieldStorage()
keys=form.keys()

if 'nickname' in keys:
    nickname = form['nickname'].value
else:
    nickname=''
if 'fullname' in keys:
    fullname = form['fullname'].value
else:
    fullname=''
if 'class' in keys:
    class_ = form['class'].value
else:
    class_=''
if 'series' in keys:
    series = form['series'].value
else:
    series=''
if 'userid' in keys:
    userid = int(form['userid'].value)
else:
    userid=0
if 'token' in keys:
    token = form['token'].value
else:
<<<<<<< HEAD
    token=0
=======
    token=''
>>>>>>> iddqd-dev/master
if 'targetid' in keys:
    targetid = int(form['targetid'].value)
else:
    targetid=0
<<<<<<< HEAD

if (not targetid or not userid or not token):
    config.returhome(31)
=======
if 'oldtargetdataids' in keys:
    oldtargetdataids=form['oldtargetdataids'].value.split(',')[:-1]
else:
    oldtargetdataids=[]
#list of all documents, old and newly uploaded
targetdatas=[]
#LOAD OLD TARGETDATA ITEMS INTO LIST
for i in oldtargetdataids:
    if 'docdata_datatypeid_'+i in keys and 'docdata_filename_'+i in keys:
        targetdatas.append(docdata())
        targetdatas[-1].datatype=form['docdata_datatypeid_'+i].value
        targetdatas[-1].obj=0
        targetdatas[-1].filename=form['docdata_filename_'+i].value
        targetdatas[-1].targetdataid=i
        if 'textarea_docdata_notes_'+i in keys:
            targetdatas[-1].notes=form['textarea_docdata_notes_'+i].value
#LOAD NEW DATA
for i in xrange(1,maxdata+1):
    if 'docdata_value_new_'+str(i) in keys and form['docdata_value_new_'+str(i)].filename:
        targetdatas.append(docdata())
        targetdatas[-1].datatype=form['docdata_datatypeid_new_'+str(i)].value
        targetdatas[-1].obj=form['docdata_value_new_'+str(i)]
        targetdatas[-1].filename=form['docdata_value_new_'+str(i)].filename
        if 'textarea_docdata_notes_new_'+str(i) in keys and form['textarea_docdata_notes_new_'+str(i)].value!='':
            targetdatas[-1].notes=form['textarea_docdata_notes_new_'+str(i)].value
##################
##DEBUG
if debug:
    print 'Content-type: text/html\n\n'
    print form
    sys.exit()
##################

if (not targetid or not userid or not token):
    config.returnhome(31)
>>>>>>> iddqd-dev/master
    sys.exit()
if (not nickname.strip()):
    print 'Location: ../edittarget.php?targetid='+str(targetid)+'&status=nonickname\n\n'
    sys.exit()

try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
<<<<<<< HEAD
        #Must be author to edit
    q.execute("SELECT authorid FROM targets WHERE targetid=%s",[targetid])
    r = q.fetchone()
    if (r[0]!=userid):
        print 'Location: ../edittarget.php?targetid='+str(targetid)+'&status=notauthor\n\n '
        sys.exit()
        #Must have valid token.
    q.execute("SELECT token FROM tokens WHERE userid=%s",[userid])
    r = q.fetchone()
    assert(r[0]==token)

    query = "UPDATE targets SET nickname=%s, fullname=%s, targetclass=%s, series=%s WHERE targetid=%s"
    options = [nickname,fullname,class_,series,targetid]
    q.execute(query,options)
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../viewtarget.php?targetid='+str(targetid)+' \n\n'
except Exception:
    config.returnhome(32)

=======
except Exception:
    config.returnhome(66)
    sys.exit()

#Check for valid token.
q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
dbtoken = q.fetchone()[0]
if(dbtoken != token):
    config.returnhome(67)
    sys.exit()

#Update target data. Must be original target author to change these fields.
#If another user tries to update this data, it will simply remain unchanged.
query = "UPDATE targets SET nickname=%s, fullname=%s, targetclass=%s, series=%s WHERE targetid=%s AND authorid=%s"
options = [nickname,fullname,class_,series,targetid,userid]
q.execute(query,options)

#Update old documentdata items (in case an author of data has updated its comment)
for i in xrange(len(oldtargetdataids)):
    if(targetdatas[i].notes!=0):
        query = 'UPDATE targetdata SET targetdatacomment=%s WHERE authorid=%s AND targetdataid=%s'
        options = [targetdatas[i].notes,userid,targetdatas[i].targetdataid]
        q.execute(query,options)

#Insert new documentdata items (in case new items have been uploaded for this target)
for i in xrange(len(oldtargetdataids),len(targetdatas)):
    query='INSERT into targetdata (targetid,datatype,targetdatacomment,authorid,dateadded) VALUES (%s,%s,%s,%s,localtimestamp) RETURNING targetdataid'
    options=[targetid,targetdatas[i].datatype,targetdatas[i].notes,userid]
    q.execute(query,options)
    targetdatas[i].targetdataid = q.fetchone()[0]

dbconn.commit()
q.close()
dbconn.close()

# FILE HANDLING #
# Prepend filename with label of targetid_targetdataid_datatypeid_
for i in xrange(len(oldtargetdataids),len(targetdatas)):
    with open('../public/uploads/targets/'+str(targetid)+'_'+str(targetdatas[i].targetdataid)+'_'+str(targetdatas[i].datatype)+'_'+str(targetdatas[i].filename),'w') as f:
        while 1:
            chunk = targetdatas[i].obj.file.read(100000)
            if not chunk:
                break
            f.write(chunk)

print 'Location: ../viewtarget.php?targetid='+str(targetid)+' \n\n'
sys.exit()
>>>>>>> iddqd-dev/master
