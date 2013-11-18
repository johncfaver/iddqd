#!/usr/bin/python
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

username=form['username'].value
userid=int(form['userid'].value)
if 'textarea_addbountycomment' in keys:
    comment=form['textarea_addbountycomment'].value
else:
    comment=0
if 'bid' in keys:
    bid=int(form['bid'].value)
else:
    bid=''
if(not bid or not userid):
    print 'Location: ../index.php?status=error'
    print ''
    exit()
if(not comment):
    print 'Location: ../bountypage.php?bid='+str(bid)
    print ''
    exit()
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    q.execute("INSERT INTO bountycomments (bountyid,bountycomment,dateadded,authorid) VALUES(%s,%s,localtimestamp,%s)",[bid,comment,userid])
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../bountypage.php?bid='+str(bid)
    print ''
except:
    print 'Location: ../index.php?status=error'
    print ''
    exit()
