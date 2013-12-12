#!/usr/bin/python

#
# Delete moldata from the editmolecule page.
#
import cgi, os, cgitb, psycopg2,subprocess
from sys import exit
from glob import iglob
cgitb.enable(display=0,logdir="../log/",format="text")
import config

docdir='../public/uploads/documents/'

os.chdir(docdir)
form=cgi.FieldStorage()
keys=form.keys()

if 'deletedataid' in keys:
    dataid = int(form['deletedataid'].value)
else:
    dataid=0
if 'molid' in keys:
    molid = int(form['molid'].value)
else:
    molid=0
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
    
if (not dataid or not molid or not userid or not token):
    config.returnhome(26)
    exit()   
try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    
        #Only authors of data can delete data entries.
    q.execute('SELECT authorid FROM moldata WHERE moldataid=%s',[dataid])
    r = q.fetchone() 
    assert(r[0]==userid)

        #Must have a valid token.
    q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
    r = q.fetchone()
    assert(r[0]==token)

    q.execute('DELETE FROM moldata WHERE moldataid=%s and authorid=%s returning molid',[dataid,userid])
    success=len(q.fetchone())
    if(success):
        q.execute('DELETE FROM datacomments WHERE dataid=%s and authorid=%s',[dataid,userid])
        dbconn.commit()
        for i in iglob(str(molid)+'_'+str(datatype)+'_'+str(dataid)+'_'+'*'):
            os.remove(i)
        print 'Location: ../editmolecule.php?molid='+str(molid)+' \n\n'
    else:
        print 'Location: ../editmolecule.php?molid='+str(molid)+'&status=notauthor \n\n'
    q.close()
    dbconn.close() 

except Exception:
    config.returnhome(27)
