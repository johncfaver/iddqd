#!/usr/bin/python

#
# Delete all information for given bounty
#

import cgi, os, cgitb, psycopg2
from sys import exit
cgitb.enable(display=0,logdir="../log/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'bid' in keys:
    bid = int(form['bid'].value)
else:
    bid=0
if 'userid' in keys:
    userid=int(form['userid'].value)
else:
    userid=0
if 'token' in keys:
    token=form['token'].value
else:
    token=0

if (not userid or not bid or not token):
    print 'Location: ../index.php?errorcode=24 \n\n'
    exit()
try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()

        #Only the person who placed the bounty can delete it.
    q.execute('SELECT placed_by_id FROM bounties WHERE bountyid=%s',[bid])
    authorid=q.fetchone()[0]
    assert(userid==authorid)
        #Must have valid token.
    q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
    dbtoken = q.fetchone()[0]
    assert(token==dbtoken)

    q.execute('DELETE FROM bounties WHERE bountyid=%s ',[bid])
    q.execute('DELETE FROM bountycomments WHERE bountyid=%s',[bid])
    dbconn.commit()
    q.close()
    dbconn.close()
    for i in ['../public/uploads/bounties/'+str(bid)+'.mol',
              '../public/uploads/bounties/'+str(bid)+'.jpg', 
              '../public/uploads/bounties/'+str(bid)+'.png']:
        if os.path.isfile(i):
            try:
                os.remove(i)
            except Exception:
                pass
    print 'Location: ../bounties.php \n\n'
except Exception:
    print 'Location: ../index.php?errorcode=25 \n\n'
