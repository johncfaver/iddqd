#!/usr/bin/env python


# Utility program for uploading a single molecule from a mol file. 
# Also look for EC50 and CC50 data in the file
# Expect a format like:
# EC50
# 34
# 500
# CC50
# 1000
# 1050
# $$$$
#

###OPTIONS###
authorid = 1
targetid = 1
molcomment = 'Imported from NNRTI SDF file'
#############

import psycopg2,sys,os,shutil
from molecule import molecule
import config
if(len(sys.argv)!=2 or sys.argv[1][-4:]!='.mol'):
    print 'Usage: importsdf.py [*.mol]'
    sys.exit()

dbconn = psycopg2.connect(config.dsn)
q = dbconn.cursor()

molfilename=sys.argv[1]
ec50s=[]
cc50s=[]
estart=0
cstart=0
try:
    with open(molfilename) as f:
        l=f.readlines()
    #Find where EC50/CC50 Data starts
    for i,j in enumerate(l):
        if j.find('EC50')>=0:
            estart=i+1
        if j.find('CC50')>=0:
            cstart=i+1    
    #Read EC50/CC50 data
    if(estart>0):
        for i in range(estart,len(l)):
            if l[i].find('CC50')>=0:
                break
            try:
                ec50s.append(float(l[i]))
            except Exception:
                pass
    if(cstart>0):
        for i in range(cstart,len(l)):
            if l[i].find('$$$$')>=0:
                break
            try:
                cc50s.append(float(l[i]))
            except Exception:
                pass
except Exception:
    print 'Problem reading experimental data.'
    sys.exit()    

try:
    #Load molecule
    molobj=molecule(molfilename)
    molweight=molobj.molweight
    molformula=molobj.formula()
except Exception:
    print 'Problem loading mol file.'
    sys.exit()

try:
    query = 'INSERT INTO molecules (molname, authorid, dateadded, molweight, molformula) VALUES (%s,%s,localtimestamp,%s,%s) RETURNING molid'
    options = [molfilename[:-4],1,molweight,molformula]
    q.execute(query,options)
    molid=q.fetchone()[0]
except Exception:
    print 'Problem inserting molecule into database.'
    sys.exit()

try:
    for i in ec50s:
        query = 'INSERT INTO moldata (molid, authorid, dateadded, targetid, value, datatype) VALUES (%s,%s,localtimestamp,%s,%s,%s)'
        options = [molid,authorid,targetid,i,2]
        q.execute(query,options)
    for i in cc50s:
        query = 'INSERT INTO moldata (molid, authorid, dateadded, value, datatype) VALUES (%s,%s,localtimestamp,%s,%s)'
        options = [molid,authorid,i,4]
        q.execute(query,options)
except Exception:
    print 'Problem inserting experimental data into database.'
    sys.exit()
        
try:
    shutil.copyfile(molfilename,'../public/uploads/structures/'+str(molid)+'-3d.mol')
except Exception:
    print 'Problem moving mol file to structure directory.'
    sys.exit()

try:
    query = 'INSERT INTO molcomments (molid,molcomment,dateadded,authorid) VALUES (%s,%s,localtimestamp,%s)'
    options = [molid,molcomment,authorid]
    q.execute(query,options)
except Exception:
    print 'Problem loading molcomment into database.'
    sys.exit()

dbconn.commit()
q.close()
dbconn.close()
print 'Successfully added: molfilename,molweight,molformula,ec50s,cc50s,molid'

