#/usr/bin/env python

def isanum(x):
    try:
        float(x)
        return True
    except Exception:    
        return False
class atom:
    def __init__(self,atsym,x,y,z,name='none'):
        self.atsym = atsym.upper()
        if len(self.atsym)>1:
            self.atsym=self.atsym[0].upper()+self.atsym[1].lower()
        self.x=float(str(x))
        self.y=float(str(y))
        self.z=float(str(z))
        self.atnum = self.atsym2atnum(atsym)
        self.name=name.strip()

    def printInfo(self):
        print self.atsym,self.x,self.y,self.z,self.name
    
    def posVec(self):
        return [self.x,self.y,self.z]
                
    def atsym2atnum(self,atsym):
        atsym=atsym.lower()
        if atsym == 'h':
            return 1
        elif atsym == 'c':
            return 6
        elif atsym == 'n':
            return 7
        elif atsym == 'o':
            return 8
        elif atsym == 'f':
            return 9
        elif atsym == 's':
            return 16 
        elif atsym == 'cl':
            return 17 
        elif atsym == 'br':
            return 35
        elif atsym == 'i':
            return 53
        else:    
            print 'Unexpected atom observed:',atsym
            return -1

