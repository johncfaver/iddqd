#!/usr/bin/python
#
# Insert user comment into database.
#
import psycopg2,cgi,cgitb
cgitb.enable(display=0,logdir="../log/",format="text")
from sys import exit
import config

form=cgi.FieldStorage()
keys=form.keys()

userid=int(form['userid'].value)
if 'textarea_addmolcomment' in keys:
    comment=form['textarea_addmolcomment'].value
else:
    comment=0
if 'molid' in keys:
    molid=int(form['molid'].value)
else:
    molid=''
if(not molid or not userid):
    print 'Location: ../index.php?errorcode=16 \n\n'
    exit()
if(not comment):
    print 'Location: ../viewmolecule.php?molid='+str(molid)+' \n\n'
    exit()
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    q.execute("INSERT INTO molcomments (molid,molcomment,dateadded,authorid) VALUES(%s,%s,localtimestamp,%s)",[molid,comment,userid])
    dbconn.commit()
    q.close()
    dbconn.close()
    print 'Location: ../viewmolecule.php?molid='+str(molid)+' \n\n'
except:
    print 'Location: ../index.php?errorcode=17 \n\n'
    exit()
