#!/usr/bin/python

#
# Delete moldata from the editemolecule page.
#
import cgi, os, cgitb, psycopg2,subprocess
cgitb.enable()

docdir='../uploads/documents/'

os.chdir(docdir)
form=cgi.FieldStorage()
keys=form.keys()

if 'deletedataid' in keys:
	dataid = int(form['deletedataid'].value)
else:
	dataid=0
if 'molid' in keys:
	molid = int(form['molid'].value)
else:
	molid=0
if 'deletedocdatatype' in keys:
	datatype= int(form['deletedocdatatype'].value)
else:
	datatype=0
if 'userid' in keys:
	userid=int(form['userid'].value)
else:
	userid=0
dbconn = psycopg2.connect("dbname=iddqddb user=iddqd password=loblaw")
q = dbconn.cursor()
q.execute('DELETE FROM moldata WHERE moldataid=%s and authorid=%s returning molid',[dataid,userid])
success=len(q.fetchone())
q.execute('DELETE FROM datacomments WHERE dataid=%s and authorid=%s',[dataid,userid])
dbconn.commit()
q.close()
rmfilename=0
if(datatype and success):
	for i in os.listdir(os.getcwd()):
		if i.find(str(molid)+'_'+str(datatype)+'_'+str(dataid)+'_')>-1:
			rmfilename=i
			break
	if(rmfilename):
		p=subprocess.Popen(['/bin/rm',rmfilename], stdout=subprocess.PIPE, stderr=subprocess.PIPE)	
print 'Location: ../editmolecule.php?molid='+str(molid)
print ''
