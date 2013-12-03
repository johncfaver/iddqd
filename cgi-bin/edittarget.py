#!/usr/bin/python

#
# Edits target in database.
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
    userid = int(form['userid'].value)
else:
    userid=0
if 'targetid' in keys:
    targetid = int(form['targetid'].value)
else:
    targetid=0

if (not targetid):
    print 'Location: ../index.php?status=error\n\n'
    sys.exit()
if (not nickname):
    print 'Location: ../edittarget.php?targetid='+str(targetid)+'status=nonickname\n\n'
    sys.exit()

try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    query = "SELECT authorid from targets where targetid=%s"
    q.execute(query,[targetid])
    r = q.fetchone()
    if (r[0]!=userid): #must be author to edit
        print 'Location: ../edittarget.php?status=notauthor\n\n'
        sys.exit()
    query = "UPDATE targets SET nickname=%s, fullname=%s, targetclass=%s, series=%s WHERE targetid=%s"
    options = [nickname,fullname,class_,series,targetid]
    q.execute(query,options)
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../viewtarget.php?targetid='+str(targetid)+' \n\n'
except Exception:
    print 'Location: ../index.php?status=error \n\n'
