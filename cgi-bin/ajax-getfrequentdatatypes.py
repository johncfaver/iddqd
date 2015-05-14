#!/usr/bin/env python

#Input  = targetid
#Output = activity data type most frequently used, default to Kd
#
# When adding new data, this saves time by guessing which types of data
# are going to be uploaded. The user won't have to change types for each 
# new entry.
#

import cgi,cgitb
import config
import psycopg2
import sys

cgitb.enable(display=0,logdir="../log/",format="text")

form=cgi.FieldStorage()
keys=form.keys()

try:
    targetid=int(form['targetid'].value)
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
  
    q.execute("SELECT COUNT(*) AS c, d.datatype, t.type FROM moldata d LEFT JOIN datatypes t ON \
                    d.datatype=t.datatypeid WHERE d.targetid = %s  AND t.class = 1 \
                    GROUP BY d.datatype, t.type ORDER BY c DESC",[targetid])
    r=q.fetchone()
    if(r is not None):
        datatypeid=r[1]
        datatype=r[2]
    else:
        datatypeid=3
        datatype='kd'

    q.close()

    print 'Content-type: text/html\n'
    print '{}'.format(datatypeid),
    sys.exit()

except Exception:
   sys.exit()
