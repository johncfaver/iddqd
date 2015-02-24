#!/usr/bin/env python

# Take molecule sketch data, send to dest

import cgi, cgitb
import subprocess
import os
import sys
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
    config.returnhome(20)
    sys.exit()

try:
    with open('../public/uploads/sketches/{}.png'.format(molid),'w') as img:
        img.write(decodestring(molfig64))   

    subprocess.Popen([os.path.join(config.convertdir,'convert'),
                    '../public/uploads/sketches/{}.png'.format(molid),
                    '-trim',
                    '../public/uploads/sketches/{}.jpg'.format(molid)],
                    stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    if dest=='am':
        print 'Location: ../addmolecule.php \n\n'
    elif dest=='vm':
        print 'Location: ../viewmolecule.php?molid={} \n\n'.format(molid)
    elif dest=='troll':
        print 'Location: ../pngwriter.php?molid={}&dest=troll \n\n'.format(molid+1)
    else:
        print 'Location: ../viewmolecule.php?molid={} \n\n'.format(molid)

except Exception:
   config.returnhome(21)
    
    
    

