#!/usr/bin/env python

#
# Inserts new bounty data into database.
# Returns to bounty page
#

import cgi, os, cgitb, base64, psycopg2, subprocess, sys
cgitb.enable(display=0,logdir="../log/",format="text")
import config

#MOVE TO UPLOAD DIRECTORY
os.chdir('../public/uploads/')
##############OPTIONS####################
debug=False
#########################################

#############Collect field data##########
form=cgi.FieldStorage()
keys=form.keys()

if debug:
    print 'Content-type: text/html\n\n'
    print 'Received the following:\n<br />'
    for i in keys:
        print i+'='+form[i].value+'<br />'
    sys.exit()    

moltext=form['moltext'].value.replace('\r','').split('\n')
molfig64=form['molfig'].value.split(',')[1]

if 'molnotes' in keys:
    molnotes=form['molnotes'].value
else:
    molnotes=0
if 'userid' in keys:
    authorid=form['userid'].value
else:
    authorid=0
if 'token' in keys:
    token = form['token'].value
else:
    token = ''
if 'select_targetid' in keys:
    targetid=form['select_targetid'].value
else:
    targetid=0
#########################################
if not targetid:
    print 'Location: ../postbounty.php \n\n'
if not token or not authorid:
    config.returnhome(59)

try:
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    q.execute('SELECT token FROM tokens WHERE userid=%s',[authorid])
    dbtoken = q.fetchone()[0]
    assert(dbtoken==token)
####################################

    ###ADD TO BOUNTY TABLE###########
    query='INSERT INTO bounties (targetid,placed_by_id,claimed,date_posted) values(%s,%s,false,localtimestamp) RETURNING bountyid'
    options=[targetid,authorid]
    q.execute(query,options)
    bid=q.fetchone()[0]
    if(molnotes):
        query='INSERT INTO bountycomments (bountyid,bountycomment,authorid,dateadded) values(%s,%s,%s,localtimestamp)'
        options=[bid,molnotes,authorid]
        q.execute(query,options)
    dbconn.commit()
    q.close()
    dbconn.close()
    
    #############FILE HANDLING###############
    with open('bounties/'+str(bid)+'.mol','w') as f:
        f.write('bounty '+str(bid)+' \n')
        for line in moltext[1:]:
            f.write(line+' \n')
    with open('bounties/'+str(bid)+'.png','w') as f:
        f.write(base64.decodestring(molfig64))
    ##########################################
    
    #############COMPUTATION##################
    subprocess.Popen([config.convertdir+'convert','bounties/'+str(bid)+'.png','-trim','bounties/'+str(bid)+'.jpg'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    ############################################
    print 'Location: ../bounties.php?success=True \n\n'
except Exception:
    config.returnhome(38)
