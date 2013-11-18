#!/usr/bin/python

#
# Delete all information for given molecule
# Only the molecule's author has can do this
#

import cgi, os, cgitb, psycopg2, subprocess
from sys import exit
cgitb.enable(display=0,logdir="../log/",format="text")
from urllib import unquote_plus
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'molid' in keys:
    molid = int(form['molid'].value)
else:
    molid=0
if 'username' in keys:
    username=unquote_plus(form['username'].value).replace('\'','').replace(';','')
else:
    username=0
if (not username or not molid):
    print 'Location: ../index.php?status=error'
    print ''
    exit()
try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    q.execute('SELECT u.username FROM molecules m LEFT JOIN users u ON m.authorid=u.userid where molid=%s',[molid])
    authorname=q.fetchone()[0]
    #Must be author to delete this molecule
    if(username!=authorname):
        print 'Location: ../index.php?status=error\n\n'
        exit()
    q.execute('DELETE FROM molecules WHERE molid=%s',[molid])
    q.execute('DELETE FROM moldata WHERE molid=%s',[molid])
    q.execute('DELETE FROM molcomments WHERE molid=%s',[molid])
    q.execute('DELETE FROM bounties WHERE molid=%s',[molid])
    dbconn.commit()
    q.close()
    dbconn.close()

    subprocess.Popen(['/bin/rm','../public/uploads/structures/'+str(molid)+'.mol','../public/uploads/structures/'+str(molid)+'-3d.mol'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    subprocess.Popen(['/bin/rm','../public/uploads/sketches/'+str(molid)+'.png','../public/uploads/sketches/'+str(molid)+'.jpg'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    subprocess.Popen(['/bin/rm','../public/uploads/documents/'+str(molid)+'*'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    subprocess.Popen(['/bin/rm','../public/uploads/qikprop/'+str(molid)+'-QP.txt'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    print 'Location: ../index.php \n\n'
except:
    print 'Location: ../index.php?status=error'
    print ''
    exit()

