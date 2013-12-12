#!/usr/bin/python


import psycopg2,cgi,cgitb,string,random
from sys import exit
cgitb.enable(display=0,logdir="../log/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'email' in keys:
    email = form['email'].value
else:
    email=0
if not email:
    config.returnhome(33)
    exit()

try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()

#Get userid to match this email address
    q.execute('SELECT userid FROM users WHERE email=%s',[email])
    if(q.rowcount==0):
        print 'Location: ../changepasswordrequestpage.php?status=bademail \n\n'
        exit()    
    r = q.fetchone() 
    userid = str(r[0])
#Check for open requests from this user in the last 24 hours.
    q.execute('SELECT daterequested from passwordchanges p where p.userid=%s and p.datechanged is null and extract(day from localtimestamp-p.daterequested) < 1', [userid])
    if(q.rowcount!=0):
        print 'Location: ../changepasswordrequestpage.php?status=openrequest'
        print ''
        exit()

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
    mailstr="Hi, \n"
    mailstr+="\tBelow is a URL for your password reset at "+config.domain+". It will expire in 24 hours. \n\n"
    mailstr+=config.domain
    mailstr+="/changepasswordpage.php?key="+changekey+" \n\n\n\n"
    mailstr+="(This was an automated message from "+config.domain+")"
    config.sendemail(email,mailstr)  
    print 'Location: ../index.php'
    print ''
except Exception: 
    config.returnhome(34)
    print ''

