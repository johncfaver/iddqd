#!/usr/bin/python

#
# Remove selcted bounty comment from database.
# Return to bountypage
#

import cgi,cgitb,psycopg2
cgitb.enable(display=0,logdir="../../private/errorlog/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

try:
    bountycommentid=int(form['bountycommentid'].value)
    bid=int(form['bid'].value)
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    q.execute('DELETE FROM bountycomments WHERE bountycommentid=%s ',[str(bountycommentid)])
    dbconn.commit()
    q.close()
    dbconn.close()
except:
    pass    


print 'Location: ../bountypage.php?bid='+str(bid)
print ''
