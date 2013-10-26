#!/usr/bin/python
#
# Marks a bounty as being claimed
# Bounty is converted into a molecule.
# Sends to molcule page
#
import psycopg2,cgi,cgitb,subprocess,shutil,sys,os
cgitb.enable()
import config

form=cgi.FieldStorage()
keys=form.keys()

userid=form['userid'].value
if 'bid' in keys:
    bid=form['bid'].value
else:
    bid=''

if(not bid):
    print 'Location: ../index.php?status=error'
    print ''
    sys.exit()
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    q.execute('SELECT pursued_by_id from bounties where bountyid=%s',[bid])
    pid = q.fetchone()[0]
    if(int(pid)==int(userid)):
        q.execute('INSERT INTO molecules (molname, authorid, dateadded) values (%s,%s,localtimestamp) RETURNING molid',['Bounty-'+str(bid),userid])
        molid = q.fetchone()[0]
        q.execute('UPDATE bounties set claimed=true, date_claimed=localtimestamp,molid=%s where bountyid=%s',[molid,bid])
        dbconn.commit()
    q.close()
    dbconn.close()
    shutil.copyfile('../uploads/bounties/'+str(bid)+'.mol','../uploads/structures/'+str(molid)+'.mol')
    shutil.copyfile('../uploads/bounties/'+str(bid)+'.png','../uploads/sketches/'+str(molid)+'.png')
    shutil.copyfile('../uploads/bounties/'+str(bid)+'.jpg','../uploads/sketches/'+str(molid)+'.jpg')
    subprocess.Popen([sys.executable,'computations.py',str(molid)],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    print 'Location: ../viewmolecule.php?molid='+str(molid)
    print ''
except:
    print 'Location: ../index.php?status=error'
    print ''
    sys.exit()
