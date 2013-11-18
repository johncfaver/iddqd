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
if (not dataid or not molid or not datatype or not userid):
    print 'Location: ../index.php?status=error'
    print ''
    exit()   
try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    #Only authors of data can delete data entries.
    q.execute('DELETE FROM moldata WHERE moldataid=%s and authorid=%s returning molid',[dataid,userid])
    success=len(q.fetchone())
    if(success):
        q.execute('DELETE FROM datacomments WHERE dataid=%s and authorid=%s',[dataid,userid])
        dbconn.commit()
        for i in iglob(str(molid)+'_'+str(datatype)+'_'+str(dataid)+'_'+'*'):
            p=subprocess.Popen(['/bin/rm',i], stdout=open(os.devnull,'w'), stderr=open(os.devnull,'w'))
    print 'Location: ../editmolecule.php?molid='+str(molid)
    print ''
    q.close()
    dbconn.close() 
except:
    print 'Location: ../index.php?status=error'
    print ''
    exit()   

