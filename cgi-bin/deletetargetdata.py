#!/usr/bin/env python

#
# Delete targetdata from the editmolecule page.
# Must be document author to delete document 
#
import cgi, os, cgitb, psycopg2
from sys import exit
from glob import iglob
cgitb.enable(display=0,logdir="../log/",format="text")
import config

targetdir='../public/uploads/targets/'

os.chdir(targetdir)
form=cgi.FieldStorage()
keys=form.keys()

if 'deletedataid' in keys:
    dataid = int(form['deletedataid'].value)
else:
    dataid=0
if 'targetid' in keys:
    targetid = int(form['targetid'].value)
else:
    targetid=0
if 'deletedocdatatype' in keys:
    datatype= int(form['deletedocdatatype'].value)
else:
    datatype=0
if 'userid' in keys:
    userid=int(form['userid'].value)
else:
    userid=0
if 'token' in keys:
    token = form['token'].value
else:
    token=''
    
if (not dataid or not targetid or not userid or not token):
    config.returnhome(32)
    exit()   
try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()

    #Must have a valid token.
    q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
    r = q.fetchone()
    assert(r[0]==token)

    #Try to delete entry from database. 
    #If this user isn't the author or the targetid doesn't match (?) then nothing happens.
    #Return 1 on success.
    q.execute('DELETE FROM targetdata WHERE targetdataid=%s and authorid=%s and targetid=%s returning 1',[dataid,userid,targetid])
    success=len(q.fetchone())
    
    if(success):
        #Delete file on server
        for i in iglob(str(targetid)+'_'+str(dataid)+'_'+str(datatype)+'_'+'*'):
            os.remove(i)
        print 'Location: ../edittarget.php?targetid='+str(targetid)+' \n\n'
    dbconn.commit()
    q.close()
    dbconn.close() 
except Exception:
    config.returnhome(69)
