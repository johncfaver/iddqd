#!/usr/bin/python

#
# Delete all information for given molecule
# Only the molecule's author can do this
#

import cgi, os, cgitb, psycopg2
from sys import exit
from glob import iglob
cgitb.enable(display=0,logdir="../log/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'molid' in keys:
    molid = int(form['molid'].value)
else:
    molid=0
if 'userid' in keys:
    userid=int(form['userid'].value)
else:
    userid=0
if 'token' in keys:
    token=form['token'].value
else:
    token=0

if (not userid or not molid or not token):
    print 'Location: ../index.php?errorcode=28 \n\n'
    exit()
try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    q.execute('SELECT authorid FROM molecules WHERE molid=%s',[molid])
    authorid=q.fetchone()[0]
   
    assert(userid==authorid) #Must be author to delete this molecule
    
    q.execute('SELECT token FROM tokens WHERE userid=%s',[userid]) 
    dbtoken = q.fetchone()[0]
    
    assert(token==dbtoken) #Must have valid token. 
     
    q.execute('DELETE FROM molecules WHERE molid=%s',[molid])
    q.execute('DELETE FROM molcomments WHERE molid=%s',[molid])
    q.execute('DELETE FROM bounties WHERE molid=%s',[molid])
    q.execute('DELETE FROM moldata WHERE molid=%s RETURNING moldataid',[molid])
    if(q.rowcount > 0):
        dataids = [ i[0] for i in q.fetchall() ]
        q.execute('DELETE FROM datacomments WHERE dataid in %s',[tuple(dataids)])
    dbconn.commit()
    q.close()
    dbconn.close()

    for i in [
        '../public/uploads/structures/'+str(molid)+'.mol',
        '../public/uploads/structures/'+str(molid)+'-3d.mol',
        '../public/uploads/sketches/'+str(molid)+'.png',
        '../public/uploads/sketches/'+str(molid)+'.jpg',
        '../public/uploads/qikprop/'+str(molid)+'-QP.txt']:
        if os.path.isfile(i):
            try:
                os.remove(i)
            except Exception:
                pass
    for i in iglob('../public/uploads/documents/'+str(molid)+'*'):
        try:
            os.remove(i)
        except Exception:
            pass
    print 'Location: ../index.php \n\n'
except Exception:
    print 'Location: ../index.php?errorcode=29 \n\n'
