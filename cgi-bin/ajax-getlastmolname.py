#!/usr/bin/env python

#Input  = targetid
#Output = suggested molname (last molname+1)
#
#e.g. if the last XXY compound was XXY144
#     then we suggest XXY145

import psycopg2,cgi,cgitb
cgitb.enable(display=0,logdir="../log/",format="text")
from sys import exit
import config

form=cgi.FieldStorage()
keys=form.keys()

try:
    targetid=int(form['targetid'].value)
    dbconn=psycopg2.connect(config.dsn)
    q=dbconn.cursor()
   
    #Retrieve series prefix. If there are multiple prefixes, prefer first entry.
    #If there is neither a prefix nor a nickname, we will suggest nothing.
    #If there is a nickname but no prefix, we will suggest a name using the nickname
    #Otherwise we will suggest a name using the nickname
    q.execute("SELECT split_part(series,',',1),nickname FROM targets where targetid=%s",[targetid])
    r=q.fetchone()
    if(not r):
        seriesprefix=''
        nickname=''
    else:
        seriesprefix=r[0]
        nickname=r[1]
        if(not seriesprefix):
            seriesprefix = nickname

    #Retrive last inhibitor entry, sorted by reverse series number (molname).
    #Assume molecules are named as (prefix)(number) e.g. XYZ1
    if (seriesprefix):
        re = '^'+seriesprefix+'\d+'
        q.execute("SELECT molname FROM molecules WHERE molname ~ %s ORDER BY molname DESC LIMIT 1",[re])
        r=q.fetchone()
        if(not r):
            lastentry=''
        else:
            lastentry=r[0]

    q.close()
    dbconn.close()

    if(not nickname and not seriesprefix):
        suggestion=''
    elif(not lastentry):
        suggestion=seriesprefix+'-1'
    else:
        try:
            newnumber=int(lastentry.lstrip(seriesprefix))+1
            suggestion=seriesprefix+'-'+str(newnumber)
        except:
            suggestion='No recommendation available.'

    print 'Content-type: text/html\n'
    print suggestion,
    exit()

except Exception:
   exit()
