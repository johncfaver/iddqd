#!/usr/bin/env python

#
#  Perform various computations on molecule after entry into molecule table and filesystem
#    1) Generate netralized 3D structures with obgen
#    2) MW and formula computation
#    3) Extensions
#
import os
import subprocess
import sys
import shutil
import psycopg2
from molecule import molecule
import config

cgidir=os.getcwd()

if len(sys.argv) != 2:
    sys.exit()

molid = int(sys.argv[1])

###GENERATE 3D MOL FILE WITH OBGEN###
os.chdir('../public/uploads/structures')
#3D conformer search with obgen
subprocess.call([os.path.join(config.babeldir,'obgen'),'{}.mol'.format(molid)],stdout=open('{}-3dt.mol'.format(molid),'w'),stderr=open(os.devnull,'w'))
#Remove warning flags
subprocess.call(['/bin/grep','-v','WARNING','{}-3dt.mol'.format(molid)],stdout=open('{}-3d.mol'.format(molid),'w'),stderr=open(os.devnull,'w'))
os.remove('{}-3dt.mol'.format(molid))
#Convert to PDB without hydrogens
subprocess.call([os.path.join(config.babeldir,'babel'),'-imol','{}-3d.mol'.format(molid),'-d','-opdb','{}-3d.pdb'.format(molid)],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
#Neutralize atoms
subprocess.call(['/bin/sed','-i',r"s/1[\+-]$//g",'{}-3d.pdb'.format(molid)],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
#Finally convert to 3D mol with hydrogens in neutral state
subprocess.call([config.babeldir+'babel','-ipdb','{}-3d.pdb'.format(molid),'-h','-omol','{}-3d.mol'.format(molid)],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
os.remove('{}-3d.pdb'.format(molid))

molobj = molecule('{}-3d.mol'.format(molid))
os.chdir(cgidir)

####UPATE MOLECULE DATA IN DATABASE############
dbconn = psycopg2.connect(config.dsn)
q = dbconn.cursor()
query = 'UPDATE molecules SET molweight=%s,molformula=%s WHERE molid=%s'
options = [str(molobj.molweight),molobj.formula(),str(molid)]
q.execute(query,options)
dbconn.commit()
q.close()
dbconn.close()

######EXTENSIONS###############
##### RUN QIKPROP##############
if(os.path.isdir('../extensions/qikprop')):
    os.chdir(qpdir)
    shutil.copyfile('../../public/uploads/structures/{}-3d.mol'.format(molid),os.path.join(os.getcwd(),'{}-3d.mol'.format(molid)))
    subprocess.call(['./qikprop','{}-3d.mol'.format(molid)], stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    shutil.move('QP.out','../../public/uploads/qikprop/{}-QP.txt'.format(molid))
    for tmpfile in ['QPmyfits','QPwarning','Similar.name','QP.CSV','QPSA.out','{}-3d.mol'.format(molid)]:
        try:
            os.remove(tmpfile)
        except Exception:
            pass

