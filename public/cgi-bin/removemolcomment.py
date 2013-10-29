#!/usr/bin/python

#
# Remove selcted molecule comment from database.
#

import cgi,cgitb,psycopg2
cgitb.enable(display=0,logdir="../../private/errorlog/",format="text")
import config

form=cgi.FieldStorage()
keys=form.keys()

try:
    molcommentid=int(form['molcommentid'].value)
    molid=int(form['molid'].value)
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
    q.execute('DELETE FROM molcomments WHERE molcommentid=%s ',[str(molcommentid)])
    dbconn.commit()
    q.close()
    dbconn.close()
except:
    pass    


print 'Location: ../viewmolecule.php?molid='+str(molid)
print ''
