#!/usr/bin/env python

# 
# passwordrequest.py
# Runs when user requests a password change.
#

import sys
import cgi,cgitb
import string
import random
import psycopg2
import config

cgitb.enable(display=0,logdir="../log/",format="text")

form=cgi.FieldStorage()
keys=form.keys()

if 'email' in keys:
    email = form['email'].value
else:
    email=0
if not email:
    config.returnhome(33)
    sys.exit()

try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()

    #Get userid matching this email address
    q.execute('SELECT userid FROM users WHERE email=%s',[email])
    if(q.rowcount==0):
        print 'Location: ../changepasswordrequestpage.php?status=bademail \n\n'
        sys.exit()    
    r = q.fetchone() 
    userid = str(r[0])
    #Check for open requests from this user in the last 24 hours.
    q.execute("""SELECT daterequested 
                    FROM passwordchanges p 
                    WHERE p.userid=%s 
                        AND p.datechanged IS NULL 
                        AND extract(day from localtimestamp-p.daterequested) < 1
              """, [userid])
    if(q.rowcount!=0):
        print 'Location: ../changepasswordrequestpage.php?status=openrequest \n\n'
        sys.exit()

    #This now appears to be a valid request.
    #End past requests. Unused requests get datechanged set to localtime and the changed variable remains false.
    q.execute('UPDATE passwordchanges SET datechanged=localtimestamp WHERE userid=%s',[userid])
    
    #Create new request
    changekey = ''.join(random.sample(string.ascii_letters + string.digits,50))
    q.execute('INSERT INTO passwordchanges (userid,daterequested,changekey) VALUES(%s,localtimestamp,%s)',[userid,changekey])
    dbconn.commit()
    q.close()
    dbconn.close()
   
    #Send email to user
    url = config.domain+"/changepasswordpage.php?key="+changekey
    mailstr="""Hi,

    Below is a URL for your password reset at {}. It will expire in 24 hours. 

    {}


    (This was an automated message from {})
            """.format(config.domain,url,config.domain)
    config.sendemail(email,mailstr)  
    config.returnhome(0)
except Exception: 
    config.returnhome(34)

