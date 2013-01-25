#!/usr/bin/python

import cgi, os, cgitb, psycopg2, subprocess, sys
cgitb.enable()
from urllib import unquote_plus

form=cgi.FieldStorage()
keys=form.keys()

if 'molid' in keys:
	molid = int(form['molid'].value)
else:
	molid=0
if 'username' in keys:
	username=unquote_plus(form['username'].value)
else:
	username=0
dbconn = psycopg2.connect("dbname=iddqddb user=iddqd password=loblaw")
q = dbconn.cursor()
q.execute('SELECT username FROM molecules m LEFT JOIN users u ON m.authorid=u.userid where molid=%s',[molid])
authorname=q.fetchone()[0]
if(username!=authorname):
	print 'Context-type: text/html\n\n'
	print username+' '+authorname
	#print 'Location: ../index.php\n\n'
	sys.exit()
q.execute('DELETE FROM molecules WHERE molid=%s',[molid])
q.execute('DELETE FROM moldata WHERE molid=%s',[molid])
q.execute('DELETE FROM molcomments WHERE molid=%s',[molid])
dbconn.commit()
q.close()
subprocess.Popen(['/bin/rm','../uploads/structures/'+str(molid)+'.mol','../uploads/structures/'+str(molid)+'-3d.mol'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
subprocess.Popen(['/bin/rm','../uploads/sketches/'+str(molid)+'.png','../uploads/sketches/'+str(molid)+'.jpg'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
subprocess.Popen(['/bin/rm','../uploads/documents/'+str(molid)+'*'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
subprocess.Popen(['/bin/rm','../uploads/qikprop/'+str(molid)+'-QP.txt'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
print 'Location: ../index.php \n\n'
