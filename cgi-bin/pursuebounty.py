#!/usr/bin/python
#
# Marks a bounty as being pursued
# Sends back to bounty page
#
import psycopg2,cgi,cgitb
from sys import exit
cgitb.enable(display=0,logdir="../log/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'userid' in keys:
    userid=int(form['userid'].value)
else:
    userid=0
if 'bid' in keys:
    bid=str(int(form['bid'].value))
else:
    bid=0

if(not bid or not userid):
    print 'Location: ../index.php?status=error \n\n'
    exit()
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    q.execute("UPDATE bounties set pursued_by_id=%s, date_pursued=localtimestamp where bountyid=%s",[userid,bid])
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../bountypage.php?bid='+bid+' \n\n'
except Exception:
    print 'Location: ../index.php?status=error \n\n'
