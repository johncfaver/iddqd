#!/usr/bin/env python

import os, cgi, cgitb, shutil, psycopg2
cgitb.enable()
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas
from reportlab.platypus import Image

uploaddir='../uploads/'

form=cgi.FieldStorage()
keys=form.keys()
if 'export' in keys:
	export=form['export'].value
if 'molids' in keys:
	molids=form['molids'].value.split(',')
if 'userid' in keys:
	userid=form['userid'].value

if export=='structures':
	dl='/tmp/structures-'+userid
	if(os.path.isdir(dl)):
		shutil.rmtree(dl)
	os.mkdir(dl)
	os.mkdir(dl+'/3d')	
	os.mkdir(dl+'/2d')	
	for i in molids:
		shutil.copyfile(uploaddir+'structures/'+i+'-3d.mol',dl+'/3d/'+i+'-3d.mol')
		shutil.copyfile(uploaddir+'structures/'+i+'.mol',dl+'/2d/'+i+'.mol')
	with open(dl+'/notebook.sdf','w') as sdf:
		for i in molids:
			with open(uploaddir+'structures/'+i+'-3d.mol') as mol:
				sdf.write(mol.read())
	filename=shutil.make_archive(dl, 'zip', root_dir=dl)
	shutil.copyfile(filename,uploaddir+'scratch/structures-'+userid+'.zip')
	print 'Location: ../uploads/scratch/structures-'+userid+'.zip'
	print ''

if export=='spreadsheet':
	dbconn = psycopg2.connect("dbname=iddqddb user=iddqd password=loblaw")
	q = dbconn.cursor()
	q.execute('select m.molname,m.molweight,r.nickname,t.type,d.value,t.units from molecules m left join moldata d on m.molid=d.molid left join targets r on d.targetid=r.targetid left join datatypes t on t.datatypeid=d.datatype where value is not null and m.molid in %s',[tuple(molids)])	
	r=q.fetchall()

	dl='../uploads/scratch/spreadsheet-'+userid+'.csv'
	with open(dl,'w') as f:
		f.write('WLJID,MW,TARGET,DATATYPE,VALUE,UNITS\n')
		for i in r:
			for j in i:
				f.write(str(j)+',')
			f.write('\n')
	print 'Location: '+dl+' \n\n'	

if export=='pdf':
	c = canvas.Canvas("../uploads/scratch/report-"+userid+".pdf", pagesize=letter)
	width,height=letter
	dbconn = psycopg2.connect("dbname=iddqddb user=iddqd password=loblaw")
	q = dbconn.cursor()
	q.execute('select m.molid,m.molname,m.molweight,r.nickname,t.type,d.value,t.units from molecules m left join moldata d on m.molid=d.molid left join targets r on d.targetid=r.targetid left join datatypes t on t.datatypeid=d.datatype where (t.units!=\'file\' or t.units is null) and m.molid in %s',[tuple(molids)])	
	r=q.fetchall()
	ystart=300
	dy=25
	for i in molids:
		c.drawImage('../uploads/sketches/'+i+'.png',50,450,width=500,height=300)
		count=0
		for j,k in enumerate(r):
			if(str(k[0])==i):
				if(count==0):
					c.drawString(width/2-50,height-50,k[1]+'  '+str(k[2])+' g/mol')	
				c.drawString(160,ystart+dy*j,''.join(str(l)+'   ' for l in k[3:] if l!=None))	
				count+=1
		c.showPage()
	c.save()
	print 'Location: ../uploads/scratch/report-'+userid+'.pdf \n\n'


