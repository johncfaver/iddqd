#!/usr/bin/python
#
# Insert bounty comment into database.
# Return to bountypage
#
import psycopg2,cgi,cgitb
from sys import exit
cgitb.enable()

form=cgi.FieldStorage()
keys=form.keys()

username=form['username'].value
userid=form['userid'].value
if 'textarea_addbountycomment' in keys:
	comment=form['textarea_addbountycomment'].value
else:
	comment=0
if 'bid' in keys:
	bid=form['bid'].value
else:
    bid=''
if(not comment):
	print 'Location: ../bountypage.php?bid='+bid
	print ''
	exit()
if(not bid):
	print 'Location: ../index.php?status=error'
	print ''
	exit()
try:
	dbconn=psycopg2.connect("dbname=iddqddb user=iddqd password=loblaw")
	q=dbconn.cursor()
	q.execute("INSERT INTO bountycomments (bountyid,bountycomment,dateadded,authorid) VALUES(%s,%s,localtimestamp,%s)",[bid,comment,userid])
	dbconn.commit()
	q.close()
	dbconn.close()
	print 'Location: ../bountypage.php?bid='+bid
	print ''
except:
	print 'Location: ../index.php?status=error'
	print ''
	exit()
