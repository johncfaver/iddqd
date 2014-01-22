#!/usr/bin/env python

#
# Generate reports,structures, or CSV files from notebooks.
#

import os, cgi, cgitb, shutil, psycopg2, subprocess
cgitb.enable(display=0,logdir="../log/",format="text")
from sys import exit
import config

uploaddir='../public/uploads'

form=cgi.FieldStorage()
keys=form.keys()
if 'export' in keys:
    export=form['export'].value
else:
    export=''
if 'molids' in keys:
    molids=form['molids'].value.split(',')
else:
    molids=''
if 'userid' in keys:
    userid=str(int(form['userid'].value))
else:
    userid=0
if 'token' in keys:
    token = form['token'].value
else:
    token = ''

if (not userid or not token or not export or not molids):
    config.returnhome(58)

if export=='structures':
    temp_path='/tmp/structures-'+userid
    if(os.path.isdir(temp_path)):
        shutil.rmtree(temp_path)
    os.mkdir(temp_path)
    os.mkdir(temp_path+'/3d')    
    os.mkdir(temp_path+'/2d')    
    with open(temp_path+'/notebook.sdf','w') as sdf:
        for i in molids:
            molfile3d=uploaddir+'/structures/'+i+'-3d.mol'
            molfile2d=uploaddir+'/structures/'+i+'.mol'
            if(os.path.isfile(molfile3d)):
                shutil.copyfile(molfile3d,temp_path+'/3d/'+i+'-3d.mol')
                with open(molfile3d) as mol:
                    sdf.write(mol.read())
            if(os.path.isfile(molfile2d)):
                shutil.copyfile(molfile2d,temp_path+'/2d/'+i+'.mol')
    filename=shutil.make_archive(temp_path, 'zip', root_dir=temp_path)
    shutil.move(filename,uploaddir+'/scratch/structures-'+userid+'.zip')
    print 'Location: ../uploads/scratch/structures-'+userid+'.zip \n\n'
    exit()

if export=='spreadsheet':
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    q.execute("SELECT m.molname,m.molweight,r.nickname,t.type,d.value,t.units \
                     FROM molecules m \
                        LEFT JOIN moldata d ON m.molid=d.molid  \
                        LEFT JOIN targets r ON d.targetid=r.targetid \
                        LEFT JOIN datatypes t ON t.datatypeid=d.datatype \
                    WHERE value IS NOT NULL AND m.molid IN %s",[tuple(molids)])    
    r=q.fetchall()
    q.close()
    dbconn.close() 
    csv_out='../public/uploads/scratch/spreadsheet-'+userid+'.csv'
    with open(csv_out,'w') as f:
        f.write('NAME,MW,TARGET,DATATYPE,VALUE,UNITS\n')
        for i in r:
            for j in i:
                f.write(str(j)+',')
            f.write('\n')
    print 'Location: ../uploads/scratch/spreadsheet-'+userid+'.csv \n\n'    
    exit()

if export=='pdf':
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    q.execute('WITH molinfo AS \
                        (SELECT m.molid,m.molname,m.molweight,m.molformula,m.dateadded,u.username \
                            FROM molecules m \
                            LEFT JOIN users u ON m.authorid=u.userid \
                            WHERE m.molid in %s),\
                    measurements AS \
                        (SELECT d.molid,d.value,t.type,t.units,r.nickname \
                            FROM moldata d \
                            LEFT JOIN targets r ON r.targetid=d.targetid \
                            LEFT JOIN datatypes t ON t.datatypeid=d.datatype \
                            WHERE d.molid in %s AND t.units!=\'file\')\
                    SELECT * from molinfo mi \
                        LEFT OUTER JOIN measurements mm ON mi.molid=mm.molid',[tuple(molids),tuple(molids)])    
    response=q.fetchall()
    q.close()
    dbconn.close()
    with open('../public/uploads/scratch/report-'+userid+'.html','w') as fout:
        htmlstr="""
            <!DOCTYPE html>
            <html>
                <head>
                    <link rel="stylesheet" href="../../../cgi-bin/report.css" type="text/css" /> 
                </head>
                <body>"""
        for mol in molids:
            for row in response:
                if str(row[0])==mol:
                    molname=str(row[1])
                    molweight=str(row[2])
                    molformula=str(row[3])
                    dateadded=row[4].strftime('%b %d, %Y %I:%M%p')
                    author=str(row[5])
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
                target=str(row[10])
                datatype=str(row[8])
                value=str(row[7])
                units=str(row[9])
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
    subprocess.call([config.wkhtmltopdfdir+'wkhtmltopdf','../public/uploads/scratch/report-'+userid+'.html','../public/uploads/scratch/report-'+userid+'.pdf'],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    print 'Location: ../uploads/scratch/report-'+userid+'.pdf \n\n'
    exit()

