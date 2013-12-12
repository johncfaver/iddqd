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
    bid=int(form['bid'].value)
else:
    bid=0
if 'token' in keys:
    token = form['token'].value
else:
    token=''

if(not bid or not userid or not token):
    config.returnhome(35)
    exit()
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
            #Check token
    q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
    dbtoken = q.fetchone()[0]
    assert(dbtoken==token)

    q.execute("UPDATE bounties SET pursued_by_id=%s, date_pursued=localtimestamp WHERE bountyid=%s",[userid,bid])
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../bountypage.php?bid='+str(bid)+' \n\n'
except Exception:
    config.returnhome(36)
