#!/usr/bin/env python

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
if 'token' in keys:
    token = form['token'].value
else:
    token = '' 

if(not userid or not bid or not cid or not token):
    config.returnhome(37)
    exit()
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
        #Check token.
    q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
    dbtoken = q.fetchone()[0]
    assert(dbtoken==token)
        #Check author.         
    q.execute("SELECT authorid FROM bountycomments WHERE bountycommentid=%s",[cid])
    aid = q.fetchone()[0]
    assert(aid==userid)

    q.execute('DELETE FROM bountycomments WHERE bountycommentid=%s ',[cid])
    dbconn.commit()
    q.close()
    dbconn.close()
except Exception:
    pass    

print 'Location: ../bountypage.php?bid='+str(bid)+' \n\n'
