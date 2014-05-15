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
    #Assume molecules are named as (prefix)?(number) e.g. XYZ1,XYZ-1,XYZ_1
    #
    #First select molnames which match re2, that is, their name begins with the series prefix, and has a number after it somewhere.
    #Then order these by molname, and take the last molname.
    #Return the first string of numbers found after the series prefix.
    #   (Do this by removing re1 from the molname, which is the series prefix.)
    #   (Then search for all strings of digits, \d+, in the remainder. Take the first match.)
    if (seriesprefix):
        re1 = '^'+seriesprefix
        q.execute("SELECT (regexp_matches(molname,'\d+$'))[1]::int as t from molecules where molname ~ %s order by t DESC limit 1",[re1])
        if q.rowcount == 1:
            r=q.fetchone()
            lastentry=r[0]
        else:
            lastentry=''
    q.close()
    dbconn.close()

    if(not nickname and not seriesprefix):
        suggestion=''
    elif(not lastentry):
        suggestion=seriesprefix+'-1'
    else:
        try:
            newnumber=int(lastentry)+1
            suggestion=seriesprefix+'-'+str(newnumber)
        except:
            suggestion='No recommendation available.'

    print 'Content-type: text/html\n'
    print suggestion,
    exit()

except Exception:
   exit()
