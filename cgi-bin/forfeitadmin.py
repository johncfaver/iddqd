#!/usr/bin/env python

##
# forfeitadmin.py
# Runs when an admin forfeits admin status
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

if (not userid or not token):
    config.returnhome(54)
    exit()

try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    
    #Check that request is from admin user with valid token.
    q.execute('SELECT count(*) FROM tokens t LEFT JOIN users u ON t.userid=u.userid WHERE u.userid=%s AND u.isadmin=true AND t.token=%s',[userid,token])
    r = q.fetchone()
    assert(r[0]==1)

    #Request seems valid, revoke admin status
    q.execute('UPDATE users SET isadmin=false WHERE userid=%s',[userid])

    dbconn.commit()
    q.close()
    dbconn.close()
    
    print 'Location: ../logout.php \n\n'

except Exception:
    config.returnhome(55)
 
