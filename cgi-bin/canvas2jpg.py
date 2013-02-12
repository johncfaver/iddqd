#!/usr/bin/env python

import cgi, cgitb, os, base64
cgitb.enable()

form = cgi.FieldStorage()

try:
	molfig64 = form['molfig'].value.split(',')[1]
	molid = int(form['molid'].value)
	with open('../uploads/sketches/'+str(molid)+'.jpg') as img:
		img.write(base64.decodestring(molfig64))	
	print 'Location: ../jpgwriter.php?molid='+str(molid+1)+'\n\n'
except:
	print 'Content-type: text/html\n\n'
	print 'Error saving jpg.'
	print '\n molid:'+str(molid)+'\n'
	print ' molfig64:'+str(molfig64)+'\n'

