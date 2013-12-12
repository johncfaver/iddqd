#!/usr/bin/python

#
# Remove selcted molecule comment from database.
# Return to molecule page
#

import cgi,cgitb,psycopg2
cgitb.enable(display=0,logdir="../log/",format="text")
from sys import exit
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'molcommentid' in keys:
    molcommentid=int(form['molcommentid'].value)
else:
    molcommentid=0
if 'molid' in keys:
    molid=int(form['molid'].value)
else:
    molid=0
if 'userid' in keys:
    userid=int(form['userid'].value)
else:
    userid=0
if 'token' in keys:
    token=form['token'].value
else:
    token=0

if (not token or not userid or not molcommentid or not molid):
    config.returnhome(44)
    exit()

try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
        #Check for token.
    q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
    dbtoken = q.fetchone()[0]
    assert(dbtoken==token)
        #Delete comment. If this fails, nothing happens, and the user is sent back to viewmolecule.
    q.execute('DELETE FROM molcomments WHERE molcommentid=%s and molid=%s and authorid=%s',[molcommentid,molid,userid])
    dbconn.commit()
    q.close()
    dbconn.close()
except Exception:
    pass    

print 'Location: ../viewmolecule.php?molid='+str(molid)
print ''
