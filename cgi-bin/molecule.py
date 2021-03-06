#!/usr/bin/env python

from atom import atom
from sys import exit 
from chem import sym2mass

class molecule:
    def __init__(self,filename,filetype=None):
        self.filename=filename
        if filetype == None:
            if filename.lower()[-3:]=='pdb':
                self.filetype='pdb'
            elif filename.lower()[-3:]=='xyz':
                self.filetype='xyz'
            elif filename.lower()[-3:]=='mol':
                self.filetype='mol'
        if self.filetype == 'xyz':
            self.charge=0
            self.molweight=0.
            self.atoms=self.readXYZfile(filename)    
            self.natoms=len(self.atoms)
        if self.filetype == 'pdb':
            self.charge=0
            self.molweight=0.
            self.atoms,self.firstatominres,self.resnames=self.readPDBfile(filename)
            self.nres=len(self.resnames)
            self.natoms=len(self.atoms)
            self.calcCharge()
        if self.filetype == 'mol':
            self.charge=0
            self.molweight=0
            self.atoms=self.readMOLfile(filename)    
            self.natoms=len(self.atoms)

    def readPDBfile(self,filename):
        try:    
            f=open(filename)
        except Exception:
            print 'ERROR LOADING ',filename
            exit()
        atomlist=[]
        firstatominres=[]
        res='1'    
        firstatominres.append(1)
        resnames=[]
        for line in f.readlines():
            if line.split()[0].strip()=='ATOM':
                atomlist.append(atom(line.split()[2][0],line.split()[5],line.split()[6],line.split()[7],line.split()[2]))
                if len(resnames)==0:
                    resnames.append(line.split()[3])
                if line.split()[4] != res:
                    firstatominres.append(len(atomlist))
                    resnames.append(line.split()[3])
                res=line.split()[4]
        return (atomlist,firstatominres,resnames)

    def readXYZfile(self,filename):
        try:    
            f=open(filename)
        except Exception:
            print 'ERROR LOADING ',filename
            return 1
        natoms=int(f.readline().strip())
        try:
            line=f.readline().strip()
            if len(line.split())==1:    
                self.charge=int(line)
            elif len(line.split())==2:
                self.charge=int(line.split()[1])
        except Exception:
            print line.split(),filename
            print 'ERROR reading XYZ file. Please put the charge on line 2.'
            exit()    
        fl=f.readlines()
        f.close()
        atomlist=[]
        for i in range(natoms):
            try:
                atomlist.append(atom(fl[i].split()[0],fl[i].split()[1],fl[i].split()[2],fl[i].split()[3]))    
                self.molweight+=sym2mass(atomlist[-1].atsym.upper())
            except Exception:
                print 'ERROR reading XYZ file. Check line', str(fl.index(i)+3),' of ',filename,'.'
                break
        return atomlist
    
    def readMOLfile(self,filename):
        try:
            f=open(filename)
        except Exception:
            print 'ERROR LOADING ',filename
            return 1
        for i in xrange(3):
            f.readline()
        natoms=int(f.readline().split()[0])
        atomlist=[]
        for i in xrange(natoms):
            try:
                line=f.readline()
                atomlist.append(atom(line.split()[3],line.split()[0],line.split()[1],line.split()[2]))
                self.molweight+=sym2mass[atomlist[-1].atsym.upper()]
            except Exception:
                print 'ERROR Reading MOL file at line:', line.split()
                break
        f.close()
        return atomlist
    
    def calcCharge(self):
        if self.filetype != 'pdb':
            return 0 
        for i in self.resnames:
            if i in ['ASP','GLU']:
                self.charge-=1    
            if i in ['LYS','ARG','HIS']:
                self.charge+=1
    
    def writeXYZfile(self,filename):
        f=open(filename,'w')
        f.write(str(len(self.atoms))+' \n')
        f.write('comment \n')
        for i in self.atoms:
            f.write(i.atsym+' '+str(i.x)+' '+str(i.y)+' '+str(i.z)+' \n')
        f.close()
    
    def printInfo(self):
        print self.filename,self.natoms,' atoms',self.charge,' charge'
        for k in self.atoms:
            k.printInfo()
    
    def formula(self):    
        symbols=[]
        counts=[]
        for i in self.atoms:
            if i.atsym in symbols:
                counts[symbols.index(i.atsym)]+=1
            else:
                symbols.append(i.atsym)
                counts.append(1)
        order=['C','H','BR','CL','F','I','N','O','P','S']
        fstr=''
        for i in order:
            if i in symbols:
                j=symbols.index(i)
                fstr+=symbols.pop(j)+str(counts.pop(j))
        for i,j in enumerate(symbols):
            fstr+=j+str(counts[i])
        return fstr
