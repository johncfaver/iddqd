#!/usr/bin/python

#
# Remove selcted bounty comment from database.
# Return to bountypage
#

import cgi,cgitb,psycopg2
cgitb.enable(display=0,logdir="../log/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

try:
    bid=int(form['bid'].value)
    bountycommentid=int(form['bountycommentid'].value)
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
