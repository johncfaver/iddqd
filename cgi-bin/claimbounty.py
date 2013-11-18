#!/usr/bin/python
#
# Marks a bounty as being claimed
# Bounty is converted into a molecule.
# Sends to molcule page
#
import psycopg2,cgi,cgitb,subprocess,shutil,sys,os
cgitb.enable(display=0,logdir="../log/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

userid=int(form['userid'].value)
if 'bid' in keys:
    bid=int(form['bid'].value)
else:
    bid=''
if(not bid):
    print 'Location: ../index.php?status=error \n\n'
    sys.exit()
try:
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    q.execute('SELECT pursued_by_id from bounties where bountyid=%s',[bid])
    pid = q.fetchone()[0]
    assert(int(pid)==userid) #Only pursuer can claim.
    q.execute('INSERT INTO molecules (molname, authorid, dateadded) values (%s,%s,localtimestamp) RETURNING molid',['Bounty-'+str(bid),str(userid)])
    molid = q.fetchone()[0]
    q.execute('UPDATE bounties set claimed=true, date_claimed=localtimestamp,molid=%s where bountyid=%s',[molid,bid])
    dbconn.commit()
    q.close()
    dbconn.close()
    shutil.copyfile('../public/uploads/bounties/'+str(bid)+'.mol','../public/uploads/structures/'+str(molid)+'.mol')
    shutil.copyfile('../public/uploads/bounties/'+str(bid)+'.png','../public/uploads/sketches/'+str(molid)+'.png')
    shutil.copyfile('../public/uploads/bounties/'+str(bid)+'.jpg','../public/uploads/sketches/'+str(molid)+'.jpg')
    subprocess.Popen([sys.executable,'computations.py',str(molid)],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    print 'Location: ../viewmolecule.php?molid='+str(molid)+' \n\n'
except Exception:
    print 'Location: ../index.php?status=error \n\n'
