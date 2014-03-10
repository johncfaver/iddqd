#!/usr/bin/env python

import json

#Parse JSON config file
with open('../config/iddqd-config.json') as configfile:
    conf = json.load(configfile)

domain = conf['domain']
babeldir = conf['babeldir'].encode('utf-8')
wkhtmltopdfdir = conf['wkhtmltopdfdir'].encode('utf-8')
convertdir = conf['convertdir'].encode('utf-8')
dbhost = conf['postgresql']['host'].encode('utf-8')
dbport = str(conf['postgresql']['port'])
dbname = conf['postgresql']['database'].encode('utf-8')
dbuser = conf['postgresql']['user'].encode('utf-8')
dbpass = conf['postgresql']['pass'].encode('utf-8')

dsn = "host="+dbhost+" port="+dbport+" dbname="+dbname+" user="+dbuser+" password="+dbpass

host = conf['email']['host'].encode('utf-8')
port = str(conf['email']['port'])
user = conf['email']['user'].encode('utf-8')
password = conf['email']['pass'].encode('utf-8')
from_address = conf['email']['from_address'].encode('utf-8')

def find_email_addresses(instring):
    import re
    reg = re.compile(r'[^@\s,=\?<>:\\\/]+@[^@\s,=\?<>:\\\/]+\.[^@\s,\?=<>:\\\/]+')
    list_of_addresses = reg.findall(instring)
    return ','.join(list_of_addresses)

#Send an email using the config in iddqd-config.json using TLS
#For invitations, password recovery, etc.
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

def returnhome(code):
    code = int(code)
    if code > 0:
        print 'Location: ../index.php?errorcode='+str(code)+' \n\n'
    else:
        print 'Location: ../index.php \n\n'

