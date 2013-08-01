#!/usr/bin/env python

from math import pi

#Physical constants in standard SI units.
h=6.626068E-34                                  #planck's J*s
c=299792458.0                                   #lightspeed m/s
k=1.3806503E-23                                 #boltzmann's J/K
R=8.3144                                        #Gas const.(k*Nav) J/K/mol
Nav=6.0221415E23                                #Avagadro's /mol
e=1.60217646E-19                                #elementary charge C
hBar=h/2/pi                                
hbar=hBar
electronMass=9.10938188E-31                     #kg
protonMass=1.67262158E-27                       #kg
bohrRad=5.2917720859E-11                        #m
amu=1.660538E-27                                #kg
kcalph=627.509391
kcalpev=23.06


def round_to_n(x, n):
    if n < 1:
        raise ValueError("number of significant digits must be >= 1")
    return "%.*f" % (n-1, x)

def mass(sym):                                   
    sym=sym.lower()
    if sym == 'h':
        return 1.00794
    elif sym == 'he':
        return 4.002602
    elif sym == "li" :
        return 6.941
    elif sym == "be":
        return 9.012182
    elif sym == 'b':
        return 10.811
    elif sym == 'c':
        return 12.0107
    elif sym == 'n':
        return  14.0067
    elif sym == 'o':
        return  15.9994
    elif sym == 'f':
        return 18.9984032
    elif sym == 'ne':
        return 20.1797
    elif sym == 'na':
        return  22.98976928
    elif sym == 'mg':
        return 24.3050
    elif sym == 'al':
        return  26.9815386
    elif sym == 'si':
        return  28.0855
    elif sym == 'p':
        return 30.973762
    elif sym == 's':
        return  32.065
    elif sym == 'cl':
        return 35.453
    elif sym == 'ar':
        return  39.948
    elif sym == 'k':
        return  39.0983
    elif sym == 'ca':
        return 40.078
    elif sym == 'sc':
        return  44.955912
    elif sym == 'ti':
        return 47.867
    elif sym == 'v':
        return  50.9415
    elif sym == 'cr':
        return 51.9961
    elif sym == 'mn':
        return  54.938045
    elif sym == 'fe':
        return 55.845
    elif sym == 'co':
        return  58.933195
    elif sym == 'ni':
        return 58.6934
    elif sym == 'cu':
        return 63.546
    elif sym == 'zn':
        return 65.38
    elif sym == 'ga':
        return  69.723
    elif sym == 'ge':
        return  72.64
    elif sym == 'as':
        return  74.92160
    elif sym == 'se':
        return  78.96
    elif sym == 'br':
        return  79.904
    elif sym == 'kr':
        return 83.798
    elif sym == 'i':
        return 126.90447
    elif sym == 'ag':
        return  107.8682
    elif sym == 'xe':
        return 131.293
    else:
        print "Element not listed."


#unit converter
def convert(number, start, end):
    if start is 'h':
        if end is 'J' or end is 'j':
            return number * 2625500 
        elif end is 'kJ' or end is 'KJ' or end is 'kj':
            return number * 2625.5 
        elif end is 'ev' or end is 'eV':
            return number * 27.2113845
        elif end is 'cal':
            return number * 627509.391 
        elif end is 'kcal':
            return number * 627.509391
        else:
            print "Those units aren't listed."
    elif start is 'j' or start is 'J':
        if end is 'cal':
            return number * 0.238845896628
        elif end is 'ev' or end is 'eV':
            return number / e
        elif end is 'kcal':
            return number * 0.238845896628E-3
        else:
            print "Those units aren't listed."
    elif start is 'kj' or start is 'kJ':
        return convert(number*1000.0, 'j', end)        
    elif start is 'cal':
        if end is 'j' or end is 'J':
            return number * 4.184
        else:
            print "Those units aren't listed."
    elif start is 'kcal':
        return convert(number*1000,'cal',end)
    elif start is 'ev' or start is 'eV':
        if end is 'J' or end is 'j':
            return number * e
        elif end is 'kJ' or end is 'kj':
            return number * e / 1000.0
        else:
            print "Those units aren't listed."
    elif start is 'Pa' or start is 'pa':
        if end is 'atm':
            return number / 101325.0
        elif end is 'torr':
            return number /101325.0*760.0
        elif end is 'bar':
            return number /1.0E5
        else:
            print "Those units aren't listed."
    elif start is 'atm':
        if end is 'Pa' or end is 'pa':
            return number * 101325.0
        else:
            print "Those units aren't listed."
    elif start is 'bar':
        if end is 'pa':
            return number * 1.0E5
        else:
            print "Those units aren't listed."
    elif start is 'C' or start is 'c':
        if end is 'F' or end is 'f':
            return number * 9.0/5.0 + 32.0
        if end is 'k' or end is 'K':
            return number + 273.15
        else:
            print "Those units aren't listed."
    elif start is 'F' or start is 'f':
        if end is 'C' or end is 'c':
            return (number-32.0)*5.0/9.0
        if end is 'K' or end is 'k':
            return convert(number,'f','c')+273.15
        else:
            print "Those units aren't listed."
    else:
        print "Those units aren't listed."
    

    
def reducedMass(a,b):
    return (a*b)/(a+b)        
def beta(t):
    return 1.0/(convert(k,'j','kcal')*Nav*t)
    
    
    
    
    
