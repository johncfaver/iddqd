#!/usr/bin/env python

##
# inviteusers.py
# Runs when an admin invites users by email.
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
if 'email_addresses' in keys:
    email_addresses = form['email_addresses'].value
else:
    email_addresses=''

if(not email_addresses or not userid or not token):
    config.returnhome(50)
    exit()

list_of_addresses = config.find_email_addresses(email_addresses).split(',')
list_of_keys = []
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    
    #Check that request is from valid admin user.
    q.execute('SELECT u.username FROM tokens t LEFT JOIN users u ON t.userid=u.userid WHERE u.userid=%s AND u.isadmin=true AND t.token=%s',[userid,token])
    assert(q.rowcount==1)
    r = q.fetchone()
    inviter = r[0]

    #Check that the email isn't already registered.
    q.execute('SELECT username FROM users WHERE email in %s',[tuple(list_of_addresses)])
    if(q.rowcount!=0):
        print 'Location: ../admin.php \n\n'
        exit()

    for i in list_of_addresses:
        changekey = ''.join(random.sample(string.ascii_letters + string.digits,50))
        list_of_keys.append(changekey)
        q.execute('INSERT into invites (email, datesent, invitekey) VALUES (%s,localtimestamp,%s)',[i,changekey])
    
    dbconn.commit()
    q.close()
    dbconn.close()

    for i,j in enumerate(list_of_addresses):
        mailstr="Hi, \n"
        mailstr+="\tYou have been invited by "+inviter+" to register an account with the Inhibitor Discovery, Design, and Quantification database at "+config.domain+". "
        mailstr+="Follow the link below to register your account.\n\n"
        mailstr+=config.domain
        mailstr+="/registerpage.php?invitekey="+list_of_keys[i]+" \n\n\n\n"
        mailstr+="(This was an automated message from "+config.domain+")"
        config.sendemail(j,mailstr)  

except Exception:
    config.returnhome(51)

print 'Location: ../admin.php \n\n'
