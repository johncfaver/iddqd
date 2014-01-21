#!/usr/bin/python

#
#  Perform various computations on molecule after entry into molecule table and filesystem
#    1) Generate 3d structures with obgen
#    2) MW and formula computation
#    3) Extras
#
import os, psycopg2, subprocess, sys, shutil
from molecule import molecule
import config

cgidir=os.getcwd()
os.chdir('../public/uploads/structures')

molid = int(sys.argv[1])

###GENERATE 3D MOL FILE WITH OBGEN###
subprocess.call([config.babeldir+'obgen',str(molid)+'.mol'],stdout=open(str(molid)+'-3dt.mol','w'),stderr=open(os.devnull,'w'))
subprocess.call(['/bin/grep','-v','WARNING',str(molid)+'-3dt.mol'],stdout=open(str(molid)+'-3d.mol','w'),stderr=open(os.devnull,'w'))
os.remove(str(molid)+'-3dt.mol')
molobj = molecule(str(molid)+'-3d.mol')

####UPATE MOLECULE DATA IN DATABASE############
dbconn = psycopg2.connect(config.dsn)
q = dbconn.cursor()
query = 'UPDATE molecules SET molweight=%s,molformula=%s WHERE molid=%s'
options = [str(molobj.molweight),molobj.formula(),str(molid)]
q.execute(query,options)
dbconn.commit()
q.close()
dbconn.close()

##### RUN QIKPROP##############
if(os.path.isdir('qikprop')):
    os.chdir(cgidir+'/qikprop')
    shutil.copyfile('../../public/uploads/structures/'+str(molid)+'-3d.mol',os.getcwd()+'/'+str(molid)+'-3d.mol')
    subprocess.call(['./qikprop',str(molid)+'-3d.mol'], stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    shutil.copyfile('QP.out','../../public/uploads/qikprop/'+str(molid)+'-QP.txt')
    for i in ['QP.out','QPmyfits','QPwarning','Similar.name','QP.CSV','QPSA.out',str(molid)+'-3d.mol']:
        try:
            os.remove(i)
        except Exception:
            pass

