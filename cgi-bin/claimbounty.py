#!/usr/bin/env python
#
# Marks a bounty as being claimed
# Bounty is converted into a molecule.
# Sends to molcule page
#
import psycopg2
import cgi,cgitb
import subprocess
import shutil
import sys
import os
import config

cgitb.enable(display=0,logdir="../log/",format="text")

form=cgi.FieldStorage()
keys=form.keys()

if 'userid' in keys:
    userid=int(form['userid'].value)
else:
    userid=0
if 'token' in keys:
    token = form['token'].value
else:
    token=''
if 'bid' in keys:
    bid=int(form['bid'].value)
else:
    bid=''

if(not bid or not userid or not token):
    config.returnhome(22)
    sys.exit()
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    #Check token
    q.execute('SELECT token FROM tokens WHERE userid=%s',[userid])
    dbtoken = q.fetchone()[0]
    assert(dbtoken==token)
    #Check pursued by. Only pursuer can claim.
    q.execute('SELECT pursued_by_id from bounties where bountyid=%s',[bid])
    pid = q.fetchone()[0]
    assert(int(pid)==userid) 

    #Bounty now becomes molecule. Default molname is "Bounty-$bid"
    molname = 'Bounty-{}'.format(bid)
    q.execute('INSERT INTO molecules (molname, authorid, dateadded) values (%s,%s,localtimestamp) RETURNING molid',[molname,str(userid)])
    molid = q.fetchone()[0]
    #Update bounty table entry; mark as claimed; link to molid
    q.execute('UPDATE bounties SET claimed=true, date_claimed=localtimestamp, molid=%s WHERE bountyid=%s',[molid,bid])
    dbconn.commit()
    q.close()
    dbconn.close()
    #Move sketches and structures from bounty folder to uploads folder
    shutil.copyfile('../public/uploads/bounties/{}.mol'.format(bid),'../public/uploads/structures/{}.mol'.format(molid))
    shutil.copyfile('../public/uploads/bounties/{}.png'.format(bid),'../public/uploads/sketches/{}.png'.format(molid))
    shutil.copyfile('../public/uploads/bounties/{}.jpg'.format(bid),'../public/uploads/sketches/{}.jpg'.format(molid))
    subprocess.Popen([sys.executable,'computations.py',str(molid),molname],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    print 'Location: ../pngwriter.php?molid={}&dest=vm \n\n'.format(molid)
except Exception:
    config.returnhome(23)
