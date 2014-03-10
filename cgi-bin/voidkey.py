#!/usr/bin/env python

##
# voidkey.py
# Runs when an admin voids and invitation key.
# Must verify that request was from admin with correct token.
#

import psycopg2, cgi,cgitb,string,random
from sys import exit
cgitb.enable(display=0,logdir="../log/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'userid' in keys:
    userid = int(form['userid'].value)
else:
    userid = 0
if 'token' in keys:
    token = form['token'].value
else:
    token=''
if 'inviteemail' in keys:
    inviteemail = form['inviteemail'].value
else:
    inviteemail=''
if 'datesent' in keys:
    datesent = form['datesent'].value
else:
    datesent=''

if (not userid or not token or not inviteemail or not datesent):
    config.returnhome(60)
    exit()

try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    
    #Check that request is from admin user with valid token.
    q.execute('SELECT count(*) FROM tokens t LEFT JOIN users u ON t.userid=u.userid WHERE u.userid=%s AND u.isadmin=true AND t.token=%s',[userid,token])
    r = q.fetchone()
    assert(r[0]==1)

    #Request seems valid, delete the deleteuserid
    q.execute('DELETE FROM invites WHERE datejoined IS NULL AND email=%s AND datesent=%s',[inviteemail,datesent])

    dbconn.commit()
    q.close()
    dbconn.close()
    
    print 'Location: ../admin.php \n\n'

except Exception:
    config.returnhome(61)
 
