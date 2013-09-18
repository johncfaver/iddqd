#!/usr/bin/python
#
# Marks a bounty as being pursued
# Sends back to bounty page
#
import psycopg2,cgi,cgitb
from sys import exit
cgitb.enable()
import credentials

form=cgi.FieldStorage()
keys=form.keys()

userid=int(form['userid'].value)
if 'bid' in keys:
    bid=form['bid'].value
else:
    bid=0

if(not bid):
    print 'Location: ../index.php?status=error'
    print ''
    exit()
try:
    dbconn=psycopg2.connect(credentials.dsn)
    q=dbconn.cursor()
    q.execute("UPDATE bounties set pursued_by_id=%s, date_pursued=localtimestamp where bountyid=%s",[userid,bid])
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../bountypage.php?bid='+bid
    print ''
except:
    print 'Location: ../index.php?status=error'
    print ''
    exit()
