#!/usr/bin/env python

#Input  = targetid
#Output = suggested molname (last molname+1)
#
#e.g. if the last XXY compound was XXY144
#     then we suggest XXY145

import psycopg2,cgi,cgitb
cgitb.enable()
from sys import exit
import credentials

form=cgi.FieldStorage()
keys=form.keys()

try:
    targetid=int(form['targetid'].value)
    dbconn=psycopg2.connect(credentials.dsn)
    q=dbconn.cursor()
   
    #Retrieve series prefix. If there are multiple prefies, prefer first entry.
    q.execute("SELECT split_part(series,',',1),nickname FROM targets where targetid=%s",[targetid])
    r=q.fetchone()
    if(not r):
        seriesprefix=''
        nickname=''
    else:
        seriesprefix=r[0]
        nickname=r[1]

    #Retrive last entry. Sorted by number.
    if (seriesprefix):
        q.execute("SELECT molname FROM molecules WHERE molname ~ %s ORDER BY molname DESC LIMIT 1",[seriesprefix])
        r=q.fetchone()
        lastentry=r[0]

    q.close()
    dbconn.close()

    if(not nickname):
        suggestion=''
    elif(not seriesprefix):
        suggestion=nickname+'001'
    elif(not lastentry):
        suggestion=seriesprefix+'001'
    else:
        newnumber=int(lastentry.lstrip(seriesprefix))+1
        suggestion=seriesprefix+'{:>3}'.format(newnumber).replace(' ','0')

    print 'Content-type: text/html\n'
    print suggestion,
    exit()

except:
   exit()