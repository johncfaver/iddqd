#!/usr/bin/env python
#
# Insert bounty comment into database.
# Return to bountypage
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
    token = form['token'].value
else:
    token=''
if 'textarea_addbountycomment' in keys:
    comment=form['textarea_addbountycomment'].value
else:
    comment=''
if 'bid' in keys:
    bid=int(form['bid'].value)
else:
    bid=''

if(not bid or not userid or not token):
    config.returnhome(14)
    exit()
if(not comment):
    print 'Location: ../bountypage.php?bid='+str(bid)+' \n\n'
    exit()

try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
        #Check token
    q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
    dbtoken = q.fetchone()[0]
    assert(dbtoken==token)

    q.execute("INSERT INTO bountycomments (bountyid,bountycomment,dateadded,authorid) VALUES(%s,%s,localtimestamp,%s)",[bid,comment,userid])

    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../bountypage.php?bid='+str(bid)+' \n\n'
except Exception:
    config.returnhome(15)
