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
    print 'Location: ../index.php?errorcode=14 \n\n'
    exit()
if(not comment):
    print 'Location: ../bountypage.php?bid='+str(bid)+' \n\n'
    exit()
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    q.execute("INSERT INTO bountycomments (bountyid,bountycomment,dateadded,authorid) VALUES(%s,%s,localtimestamp,%s)",[bid,comment,userid])
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../bountypage.php?bid='+str(bid)+' \n\n'
except Exception:
    print 'Location: ../index.php?errorcode=15 \n\n'
