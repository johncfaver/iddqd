#!/usr/bin/env python

#
# Adds new target to database.
# Returns user to target page.
#
import cgi, cgitb, psycopg2, sys
cgitb.enable(display=0,logdir="../log/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'nickname' in keys:
    nickname = form['nickname'].value
else:
    nickname=''
if 'fullname' in keys:
    fullname = form['fullname'].value
else:
    fullname=''
if 'class' in keys:
    class_ = form['class'].value
else:
    class_=''
if 'series' in keys:
    series = form['series'].value
else:
    series=''
if 'userid' in keys:
    authorid = int(form['userid'].value)
else:
    authorid=0
if 'token' in keys:
    token = form['token'].value
else:
    token=0

if (not nickname):
    print 'Location: ../addtarget.php?status=nonickname\n\n'
    sys.exit()
if (not authorid or not token):
    config.returnhome(18)
    sys.exit()

try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
        #Check for token.
    q.execute('SELECT token FROM tokens WHERE userid=%s',[authorid])
    dbtoken = q.fetchone()[0]
    assert(dbtoken==token)

        #Add target to database.
    query = "INSERT INTO targets (nickname,fullname,targetclass,series,authorid,dateadded) VALUES(%s,%s,%s,%s,%s,localtimestamp)"
    options = [nickname,fullname,class_,series,authorid]
    q.execute(query,options)
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../targets.php \n\n'
except Exception:
    config.returnhome(19)

