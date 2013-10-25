#!/usr/bin/python


import psycopg2,cgi,cgitb,string,random
from sys import exit
cgitb.enable()
import credentials

form=cgi.FieldStorage()
keys=form.keys()

if 'username' in keys:
    username = form['username'].value
else:
    username=0
if 'email' in keys:
    email = form['email'].value
else:
    email=0
if not email or not username:
    print 'Location: ../index.php?status=error'
    print ''
    exit()

try:
    dbconn=psycopg2.connect(credentials.dsn)
    q=dbconn.cursor()
#Get userid
    q.execute('SELECT userid FROM users WHERE username=%s and email=%s',[username,email])
    r = q.fetchone() 
    assert(len(r) == 1), "Username and email don't match."
    userid = str(r[0])
#End past requests. Unused requests get datechanged set to localtime while changed=false.
    q.execute('UPDATE passwordchanges SET datechanged=localtimestamp WHERE userid=%s',[userid])
#Create new request
    changekey = ''.join(random.choice(string.ascii_letters + string.digits) for x in range(50))
    q.execute('INSERT INTO passwordchanges (userid,daterequested,changekey) VALUES(%s,localtimestamp,%s)',[userid,changekey])
    dbconn.commit()
    q.close()
    dbconn.close()
#Send email to user
    mailstr="Dear "+username+", \n"
    mailstr+="\tHere is a URL for your password reset to IDDQD: \n\n"
    mailstr+=credentials.domain
    mailstr+="/changepasswordpage.php?key="+changekey+" \n\n"
    mailstr+="This was an automated message from "+credentials.domain
    credentials.sendemail(email,mailstr)  
    print 'Location: ../index.php'
    print ''
except: 
    print 'Location: ../index.php?status=error'
    print ''

