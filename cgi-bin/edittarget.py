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
if 'token' in keys:
    token = form['token'].value
else:
    token=0
if 'targetid' in keys:
    targetid = int(form['targetid'].value)
else:
    targetid=0

if (not targetid or not userid or not token):
    print 'Location: ../index.php?errorcode=31 \n\n'
    sys.exit()
if (not nickname):
    print 'Location: ../edittarget.php?targetid='+str(targetid)+'status=nonickname\n\n'
    sys.exit()

try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
        #Must be author to edit
    q.execute("SELECT authorid FROM targets WHERE targetid=%s",[targetid])
    r = q.fetchone()
    if (r[0]!=userid):
        print 'Location: ../edittarget.php?targetid='+str(targetid)+'&status=notauthor\n\n '
        sys.exit()
        #Must have valid token.
    q.execute("SELECT token FROM tokens WHERE userid=%s",[userid])
    r = q.fetchone()
    assert(r[0]==token)

    query = "UPDATE targets SET nickname=%s, fullname=%s, targetclass=%s, series=%s WHERE targetid=%s"
    options = [nickname,fullname,class_,series,targetid]
    q.execute(query,options)
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../viewtarget.php?targetid='+str(targetid)+' \n\n'
except Exception:
    print 'Location: ../index.php?errorcode=32 \n\n'

