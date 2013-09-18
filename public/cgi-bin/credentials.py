#!/usr/bin/env python

import json
from sys import exit

with open('../../private/iddqd-config.json') as configfile:
    cred = json.load(configfile)

babeldir = cred['babeldir'].encode('utf-8')
dbhost = cred['postgresql']['host'].encode('utf-8')
dbport = str(cred['postgresql']['port'])
dbname = cred['postgresql']['database'].encode('utf-8')
dbuser = cred['postgresql']['user'].encode('utf-8')
dbpass = cred['postgresql']['pass'].encode('utf-8')

dsn = "host="+dbhost+" port="+dbport+" dbname="+dbname+" user="+dbuser+" password="+dbpass

