#/usr/bin/env python

from chem import sym2num

class atom:
    def __init__(self,atsym,x,y,z,name=None):
        if len(atsym)==2:
            self.atsym=atsym[0].upper()+atsym[1].lower()
        else:
            self.atsym=atsym[0].upper()        
        self.x=float(x)
        self.y=float(y)
        self.z=float(z)
        self.atnum = sym2num[self.atsym.upper()]
        if(name != None):
            self.name=name.strip()

    def printInfo(self):
        print self.atsym,self.x,self.y,self.z,self.name
    
    def posVec(self):
        return [self.x,self.y,self.z]
                
