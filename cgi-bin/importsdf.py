#!/usr/bin/env python


#
# utility program for uploading many molecules in an sdf file. 
#
import psycopg2,sys,os,shutil
from molecule import molecule

if(len(sys.argv)!=2 or sys.argv[1][-4:]!='.mol'):
    print 'Usage: importsdf.py [*.mol]'
    sys.exit()

dbconn = psycopg2.connect("dbname=iddqddb user=iddqd password=loblaw")
q = dbconn.cursor()

molfilename=sys.argv[1]
ec50s=[]
cc50s=[]
estart=0
cstart=0
with open(molfilename) as f:
    l=f.readlines()
for i,j in enumerate(l):
    if j.find('EC50')>=0:
        estart=i+1
    if j.find('CC50')>=0:
        cstart=i+1    
if(estart>0):
    for i in range(estart,len(l)):
        if l[i].find('CC50')>=0:
            break
        try:
            ec50s.append(eval(l[i]))
        except:
            pass
if(cstart>0):
    for i in range(cstart,len(l)):
        if l[i].find('$$$$')>=0:
            break
        try:
            cc50s.append(eval(l[i]))
        except:
            pass


molobj=molecule(molfilename)
molweight=molobj.molweight
molformula=molobj.formula()
query = 'INSERT INTO molecules (molname, authorid, dateadded, molweight, molformula) VALUES (%s,%s,localtimestamp,%s,%s) RETURNING molid'
options = [molfilename[:-4],1,molweight,molformula]
q.execute(query,options)
molid=q.fetchone()[0]

for i in ec50s:
    query = 'INSERT INTO moldata (molid, authorid, dateadded, targetid, value, datatype) VALUES (%s,%s,localtimestamp,%s,%s,%s)'
    options = [molid,1,1,i,2]
    q.execute(query,options)
for i in cc50s:
    query = 'INSERT INTO moldata (molid, authorid, dateadded, value, datatype) VALUES (%s,%s,localtimestamp,%s,%s)'
    options = [molid,1,i,4]
    q.execute(query,options)
if not os.path.isdir('structures'):
    os.mkdir('structures')
shutil.copyfile(os.getcwd()+'/'+molfilename,os.getcwd()+'/structures/'+str(molid)+'-3d.mol')

molcomment='Imported from NNRTI SDF file'
query = 'INSERT INTO molcomments (molid,molcomment,dateadded,authorid) VALUES (%s,%s,localtimestamp,%s)'
options = [molid,molcomment,1]
q.execute(query,options)

dbconn.commit()
q.close()
dbconn.close()
print molfilename,molweight,molformula,ec50s,cc50s,molid

