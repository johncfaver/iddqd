#!/usr/bin/env python

import cgi, cgitb
cgitb.enable(display=0,logdir="../log/",format="text")
from base64 import decodestring

form = cgi.FieldStorage()

try:
    molfig64 = form['molfig'].value.split(',')[1]
    molid = int(form['molid'].value)
    with open('../public/uploads/sketches/'+str(molid)+'.png','w') as img:
        img.write(base64.decodestring(molfig64))    
    print 'Location: ../pngwriter.php?molid='+str(molid+1)+'\n\n'
except:
    print 'Content-type: text/html\n\n'
    print 'Error saving png.'
    print '\n molid:'+str(molid)+'\n'
    print ' molfig64:'+str(molfig64)+'\n'

