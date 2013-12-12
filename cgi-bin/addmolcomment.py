#!/usr/bin/python
#
# Insert molecule comment into database.
#
import psycopg2,cgi,cgitb
cgitb.enable(display=0,logdir="../log/",format="text")
from sys import exit
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'userid' in keys:
    userid=int(form['userid'].value)
else:
    userid=0
if 'token' in keys:
    token=form['token'].value
else:
    token=0
if 'textarea_addmolcomment' in keys:
    comment=form['textarea_addmolcomment'].value
else:
    comment=0
if 'molid' in keys:
    molid=int(form['molid'].value)
else:
    molid=0

if(not molid or not userid or not token):
    config.returnhome(16)
    exit()
if(not comment):
    print 'Location: ../viewmolecule.php?molid='+str(molid)+' \n\n'
    exit()

try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
        #Check for token
    q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
    dbtoken = q.fetchone()[0]
    assert(dbtoken==token)
    q.execute("INSERT INTO molcomments (molid,molcomment,dateadded,authorid) VALUES(%s,%s,localtimestamp,%s)",[molid,comment,userid])
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../viewmolecule.php?molid='+str(molid)+' \n\n'
except Exception:
    config.returnhome(17)
    exit()
