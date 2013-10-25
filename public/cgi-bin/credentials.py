#!/usr/bin/env python

import json

#Parse JSON config file
with open('../../private/iddqd-config.json') as configfile:
    cred = json.load(configfile)
domain = cred['domain']
babeldir = cred['babeldir'].encode('utf-8')
dbhost = cred['postgresql']['host'].encode('utf-8')
dbport = str(cred['postgresql']['port'])
dbname = cred['postgresql']['database'].encode('utf-8')
dbuser = cred['postgresql']['user'].encode('utf-8')
dbpass = cred['postgresql']['pass'].encode('utf-8')

host = cred['email']['host'].encode('utf-8')
port = str(cred['email']['port'])
user = cred['email']['user'].encode('utf-8')
password = cred['email']['pass'].encode('utf-8')
from_address = cred['email']['from_address'].encode('utf-8')


dsn = "host="+dbhost+" port="+dbport+" dbname="+dbname+" user="+dbuser+" password="+dbpass


#Send an email using the credentials in iddqd-config.json using TLS
#For password recovery, etc.
def sendemail(to,text):
    import smtplib
    from email.mime.text import MIMEText

    msg = MIMEText(text)
    msg['Subject'] = 'IDDQD Message'
    msg['From'] = from_address
    msg['To'] = to

    s = smtplib.SMTP(host,port)
    s.ehlo()
    s.starttls()
    s.ehlo()
    s.login(user,password)

    s.sendmail(from_address,to,msg.as_string())
    s.quit()

