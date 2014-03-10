#!/usr/bin/env python

#
# Utility script for convering sdf file to individual mol files.
# Assumes that individual molecules are separted by $$$$ lines
#
import sys, os

if(len(sys.argv)!=2 or sys.argv[1][-4:]!='.sdf'):
    print 'Usage: importsdf.py [*.sdf]'
    sys.exit()
sdffilename=sys.argv[1]
molstarts=[0]
with open(sdffilename) as f:
    for i,j in enumerate(f.readlines()):
        if j.find('$$$$')>=0:
            molstarts.append(i+1)
print 'Found ',len(molstarts),' molecules.'
dirname=sdffilename[:-3]+'-molfiles'
os.mkdir(dirname)
with open(sdffilename) as f:
    mnum=0
    for i,j in enumerate(f.readlines()):
        if i in molstarts:
            mnum+=1
            molname=j.strip()
            mfile=open(dirname+'/'+molname+'.mol','w')
        if i < molstarts[mnum]:
            mfile.write(j)
        if i == molstarts[mnum]-1:
            mfile.close()


    
