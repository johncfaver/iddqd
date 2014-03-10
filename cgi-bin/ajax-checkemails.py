#!/usr/bin/env python


from config import find_email_addresses
import cgi,cgitb
cgitb.enable(display=0,logdir="../log/",format="text")
from sys import exit
from urllib import unquote_plus

form=cgi.FieldStorage()

try:
    print 'Content-type: text/html \n'
    emailstr = unquote_plus(form['emailstr'].value)
    outstring = find_email_addresses(emailstr)
    print outstring.replace(',','\r\n'),
except Exception:
    exit()
