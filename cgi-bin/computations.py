#!/usr/bin/python

import os, psycopg2, subprocess, sys
from molecule import molecule

cgidir=os.getcwd()
os.chdir('../uploads/structures')

molid = int(sys.argv[1])

###GENERATE 3D MOL FILE WITH OBGEN###
subprocess.call(['/usr/bin/obgen',str(molid)+'.mol'],stdout=open(str(molid)+'-3dt.mol','w'),stderr=open(os.devnull,'w'))
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
os.chdir(cgidir+'/qikprop')
subprocess.call(['/bin/cp','../../uploads/structures/'+str(molid)+'-3d.mol','.'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
subprocess.call(['./qikprop',str(molid)+'-3d.mol'], stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
subprocess.call(['/bin/cp','QP.out','../../uploads/qikprop/'+str(molid)+'-QP.txt'], stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
subprocess.call(['/bin/rm','QP.out','QPmyfits','QPwarning','Similar.name','QP.CSV','QPSA.out',str(molid)+'-3d.mol'], stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))

