#!/usr/bin/env python

#
# Generate reports,structures, or CSV files from notebooks.
#

import os, cgi, cgitb, shutil, psycopg2, subprocess
cgitb.enable()
from sys import exit
import credentials

uploaddir='../uploads/'

form=cgi.FieldStorage()
keys=form.keys()
if 'export' in keys:
    export=form['export'].value
if 'molids' in keys:
    molids=form['molids'].value.split(',')
if 'userid' in keys:
    userid=form['userid'].value

if export=='structures':
    dl='/tmp/structures-'+userid
    if(os.path.isdir(dl)):
        shutil.rmtree(dl)
    os.mkdir(dl)
    os.mkdir(dl+'/3d')    
    os.mkdir(dl+'/2d')    
    for i in molids:
        shutil.copyfile(uploaddir+'structures/'+i+'-3d.mol',dl+'/3d/'+i+'-3d.mol')
        shutil.copyfile(uploaddir+'structures/'+i+'.mol',dl+'/2d/'+i+'.mol')
    with open(dl+'/notebook.sdf','w') as sdf:
        for i in molids:
            with open(uploaddir+'structures/'+i+'-3d.mol') as mol:
                sdf.write(mol.read())
    filename=shutil.make_archive(dl, 'zip', root_dir=dl)
    shutil.copyfile(filename,uploaddir+'scratch/structures-'+userid+'.zip')
    print 'Location: ../uploads/scratch/structures-'+userid+'.zip'
    print ''
    exit()

if export=='spreadsheet':
    dbconn = psycopg2.connect(credentials.dsn)
    q = dbconn.cursor()
    q.execute('select m.molname,m.molweight,r.nickname,t.type,d.value,t.units from molecules m left join moldata d on m.molid=d.molid left join targets r on d.targetid=r.targetid left join datatypes t on t.datatypeid=d.datatype where value is not null and m.molid in %s',[tuple(molids)])    
    r=q.fetchall()

    dl='../uploads/scratch/spreadsheet-'+userid+'.csv'
    with open(dl,'w') as f:
        f.write('NAME,MW,TARGET,DATATYPE,VALUE,UNITS\n')
        for i in r:
            for j in i:
                f.write(str(j)+',')
            f.write('\n')
    print 'Location: '+dl+' \n\n'    
    exit()

if export=='pdf':
    dbconn = psycopg2.connect(credentials.dsn)
    q = dbconn.cursor()
    q.execute('WITH molinfo AS \
                    (SELECT m.molid,m.molname,m.molweight,m.molformula,m.dateadded,u.username \
                        FROM molecules m LEFT JOIN users u ON m.authorid=u.userid \
                        WHERE m.molid in %s),\
                    measurements AS \
                    (SELECT d.molid,d.value,t.type,t.units,r.nickname \
                        FROM moldata d LEFT JOIN targets r ON r.targetid=d.targetid \
                        LEFT JOIN datatypes t ON t.datatypeid=d.datatype \
                        WHERE d.molid in %s AND t.units!=\'file\')\
                    SELECT * from molinfo mi LEFT OUTER JOIN measurements mm ON mi.molid=mm.molid',[tuple(molids),tuple(molids)])    
    response=q.fetchall()

    with open('../uploads/scratch/report-'+userid+'.html','w') as fout:
        htmlstr="""
            <!DOCTYPE html>
            <html>
                <head>
                    <link rel="stylesheet" href="../../cgi-bin/report.css" type="text/css" /> 
                </head>
                <body>"""
        for mol in molids:
            for row in response:
                if str(row[0])==mol:
                    molname=row[1]
                    molweight=str(row[2])
                    molformula=row[3]
                    dateadded=row[4].strftime('%b %d, %Y %I:%M%p')
                    author=row[5]
                    break
            htmlstr+='<h1>'+molname+'</h1>'
            htmlstr+='<br /><br /><div id="infodiv"><table id="infotable">'
            htmlstr+='<tr><td class="infotd">Added by</td><td class="infotd infotdleft">'+author+'</td></tr>'
            htmlstr+='<tr><td class="infotd">Added on</td><td class="infotd infotdleft">'+dateadded+'</td></tr>'
            htmlstr+='<tr><td class="infotd">MW</td><td class="infotd infotdleft">'+molweight+'</td></tr>'
            htmlstr+='<tr><td class="infotd">Formula</td><td class="infotd infotdleft">'+molformula+'</td></tr>'
            htmlstr+='<tr><td class="infotd">IUPAC</td><td class="infotd infotdleft"></td></tr>'
            htmlstr+='<tr><td class="infotd">CAS</td><td class="infotd infotdleft"></td></tr>'
            htmlstr+='</table></div>'
            htmlstr+='<img src="../sketches/'+str(mol)+'.png" /><br />'
            htmlstr+='<table id="datatable">'
            htmlstr+='''<tr>
                            <th class="datath datathleft">Data</th>
                            <th class="datath datathleft">Value</th>
                            <th class="datath datathleft">Units</th>
                            <th class="datath datathright">Target</th>
                       </tr>
                     '''
            for row in response:
                if str(row[0])!=mol:
                    continue
                target=row[10]
                datatype=row[8]
                value=str(row[7])
                units=row[9]
                if(not value or not datatype or not units):
                    continue
                if(units):
                    units=units.decode('utf-8').encode('latin-1')
                if(not target):
                    target='-'
                htmlstr+='<tr><td class="datatd datatdleft">'+datatype+'</td>'
                htmlstr+='<td class="datatd">'+value+'</td>'
                htmlstr+='<td class="datatd">'+units+'</td>'
                htmlstr+='<td class="datatd datatdright">'+target+'</td></tr>'
            htmlstr+='</table>' 

            htmlstr+='<div class="clearfloat"></div>'
            htmlstr+='<div class="pagebreak"><span style="display:none;">&nbsp;</span></div>'        
        htmlstr+="""
                </body>
            </html>
        """
        fout.write(htmlstr)
    subprocess.call(['/usr/bin/wkhtmltopdf','../uploads/scratch/report-'+userid+'.html','../uploads/scratch/report-'+userid+'.pdf'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))

    print 'Location: ../uploads/scratch/report-'+userid+'.pdf \n\n'
    exit()

