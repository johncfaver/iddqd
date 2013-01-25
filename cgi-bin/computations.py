#!/usr/bin/python

import os, psycopg2, subprocess, sys
from molecule import molecule

maindir='/var/www/iddqd-prod/'
os.chdir(maindir+'uploads/structures')

molid = int(sys.argv[1])

###GENERATE 3D MOL FILE###
subprocess.call(['/usr/local/bin/obgen',str(molid)+'.mol'],stdout=open(str(molid)+'-3dt.mol','w'),stderr=open(os.devnull,'w'))
subprocess.call(['/bin/grep','-v','WARNING',str(molid)+'-3dt.mol'],stdout=open(str(molid)+'-3d.mol','w'),stderr=open(os.devnull,'w'))
subprocess.Popen(['/bin/rm',str(molid)+'-3dt.mol'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
molobj = molecule(str(molid)+'-3d.mol')

####UPATE MOLECULE DATA IN DATABASE############
dbconn = psycopg2.connect("dbname=iddqddb user=iddqd password=loblaw")
q = dbconn.cursor()
query = 'UPDATE molecules SET molweight=%s,molformula=%s WHERE molid=%s'
options = [str(molobj.molweight),molobj.formula(),str(molid)]
q.execute(query,options)
dbconn.commit()
q.close()
dbconn.close()

##### RUN QIKPROP##############
os.chdir(maindir+'cgi-bin/qikprop')
subprocess.call(['/bin/cp','/var/www/iddqd-prod/uploads/structures/'+str(molid)+'-3d.mol','.'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
subprocess.call(['/home/john/QikProp/qikprop',str(molid)+'-3d.mol'], stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
subprocess.call(['/bin/cp','QP.out','/var/www/iddqd-prod/uploads/qikprop/'+str(molid)+'-QP.txt'], stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
subprocess.call(['/bin/rm','QP.out','Similar.name','QP.CSV','QPSA.out',str(molid)+'-3d.mol'], stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))


