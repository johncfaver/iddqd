#!/usr/bin/python

#
# Delete all information for given bounty
#

import cgi, os, cgitb, psycopg2, subprocess
from sys import exit
cgitb.enable(display=0,logdir="../log/",format="text")
from urllib import unquote_plus
import config

form=cgi.FieldStorage()
keys=form.keys()

if 'bid' in keys:
    bid = int(form['bid'].value)
else:
    bid=0
if 'username' in keys:
    username=unquote_plus(form['username'].value).replace('\'','').replace(';','')
else:
    username=0
if (not username or not bid):
    print 'Location: ../index.php?status=error'
    print ''
    exit()
try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    q.execute('SELECT username FROM bounties b LEFT JOIN users u ON b.placed_by_id=u.userid where bountyid=%s',[bid])
    authorname=q.fetchone()[0]
    if(username!=authorname): #only the person who placed the bounty can delete it.
        print 'Location: ../index.php?status=error\n\n'
        exit()
    q.execute('DELETE FROM bounties WHERE bountyid=%s returning claimed,molid',[bid])
    claimed,molid = q.fetchone()
    if claimed:
        q.execute('DELETE FROM molecules where molid=%s',[molid])
        q.execute('DELETE FROM molcomments where molid=%s',[molid])
    q.execute('DELETE FROM bountycomments WHERE bountyid=%s',[bid])
    dbconn.commit()
    q.close()
    dbconn.close()
    subprocess.Popen(['/bin/rm','../public/uploads/bounties/'+str(bid)+'.mol','../public/uploads/bounties/'+str(bid)+'.jpg', '../public/uploads/bounties/'+str(bid)+'.png'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    if claimed:
        subprocess.Popen(['/bin/rm','../public/uploads/structures/'+str(molid)+'.mol','../public/uploads/sketches/'+str(molid)+'.jpg', '../public/uploads/sketches/'+str(molid)+'.png','../public/uploads/structures/'+str(molid)+'-3d.mol'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    print 'Location: ../bounties.php \n\n'
except:
    print 'Location: ../index.php?status=error'
    print ''
    exit()
