#!/usr/bin/env python

# Take molecule sketch data, send to dest

import cgi, cgitb, subprocess, os
from base64 import decodestring
import config
cgitb.enable(display=0,logdir="../log/",format="text")

form = cgi.FieldStorage()
keys = form.keys()

if 'molfig' in keys:
    molfig64 = form['molfig'].value.split(',')[1]
else:
    molfig64=0
if 'molid' in keys:
    molid = int(form['molid'].value)
else:
    molid=0
if 'dest' in keys:
    dest = form['dest'].value
else:
    dest=0

if (not molfig64 or not molid):
    print 'Location: ../index.php?status=error \n\n'

try:
    with open('../public/uploads/sketches/'+str(molid)+'.png','w') as img:
        img.write(decodestring(molfig64))    
    subprocess.Popen([config.convertdir+'convert',
                '../public/uploads/sketches/'+str(molid)+'.png',
                '-trim',
                '../public/uploads/sketches/'+str(molid)+'.jpg'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    if dest=='am':
        print 'Location: ../addmolecule.php \n\n'
    elif dest=='vm':
        print 'Location: ../viewmolecule.php?molid='+str(molid)+' \n\n'
    elif dest=='troll':
        print 'Location: ../pngwriter.php?molid='+str(molid+1)+'&dest=troll \n\n' 
    else:
        print 'Location: ../viewmolecule.php?molid='+str(molid)+' \n\n'

except Exception:
    print 'Content-type: text/html\n\n'
    print 'Error saving png.'
    print '\n molid:'+str(molid)+'\n'
    print ' molfig64:'+str(molfig64)+'\n'

