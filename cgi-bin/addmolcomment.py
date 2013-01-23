#!/usr/bin/python

import psycopg2,cgi,cgitb
from sys import exit
cgitb.enable()

form=cgi.FieldStorage()
keys=form.keys()

username=form['username'].value
userid=form['userid'].value
if 'textarea_addmolcomment' in keys:
	comment=form['textarea_addmolcomment'].value
else:
	comment=0
molid=form['molid'].value
if(not comment):
	print 'Location: ../viewmolecule.php?molid='+molid
	print ''
	exit()
dbconn=psycopg2.connect("dbname=iddqddb user=iddqd password=loblaw")
q=dbconn.cursor()
q.execute("INSERT INTO molcomments (molid,molcomment,dateadded,authorid) values(%s,%s,localtimestamp,%s)",[molid,comment,userid])
dbconn.commit()
q.close()
dbconn.close()
print 'Location: ../viewmolecule.php?molid='+molid
print ''
