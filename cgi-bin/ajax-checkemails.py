#!/usr/bin/env python


import sys
import cgi,cgitb
from urllib import unquote_plus
from config import find_email_addresses

cgitb.enable(display=0,logdir="../log/",format="text")

form=cgi.FieldStorage()

try:
    print 'Content-type: text/html \n'
    emailstr = unquote_plus(form['emailstr'].value)
    outstring = find_email_addresses(emailstr)
    print outstring.replace(',','\r\n'),
except Exception:
    sys.exit()
