#!/usr/bin/python

#
# Remove selcted bounty comment from database.
# Return to bountypage
#

import cgi,cgitb,psycopg2
from sys import exit
cgitb.enable(display=0,logdir="../log/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'bid' in keys:
    bid = int(form['bid'].value)
else:
    bid=0
if 'bountycommentid' in keys:
    cid = int(form['bountycommentid'].value)
else:
    cid=0
if 'userid' in keys:
    userid = int(form['userid'].value)
else:
    userid=0
if(not userid or not bid or not cid):
    print 'Location: ../index.php?errorcode=37 \n\n'
    exit()
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    q.execute("SELECT authorid from bountycomments where bountycommentid=%s",[cid])
    aid = q.fetchone()[0]
    assert(aid==userid)
    q.execute('DELETE FROM bountycomments WHERE bountycommentid=%s ',[cid])
    dbconn.commit()
    q.close()
    dbconn.close()
except Exception:
    pass    

print 'Location: ../bountypage.php?bid='+str(bid)+' \n\n'
