#!/usr/bin/env python

#
# Generate reports,structures, or CSV files from notebooks.
#

import os
import sys
import cgi, cgitb
import shutil
import subprocess
import config
import psycopg2

cgitb.enable(display=0,logdir="../log/",format="text")

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

if export == 'structures':
    #Create zip file with 2d and 3d structures and an sdf file.
    temp_path='/tmp/structures-{}'.format(userid)
    if(os.path.isdir(temp_path)):
        shutil.rmtree(temp_path)
    os.mkdir(temp_path)
    os.mkdir(os.path.join(temp_path,'3d'))
    os.mkdir(os.path.join(temp_path,'2d'))    
    with open(os.path.join(temp_path,'notebook.sdf'),'w') as sdf:
        for imol in molids:
            molfile3d=os.path.join(uploaddir,'structures','{}-3d.mol'.format(imol))
            molfile2d=os.path.join(uploaddir,'structures','{}.mol'.format(imol))
            if(os.path.isfile(molfile3d)):
                shutil.copyfile(molfile3d,os.path.join(temp_path,'3d','{}-3d.mol'.format(imol)))
                with open(molfile3d) as tmolfile:
                    sdf.write(tmolfile.read())
            if(os.path.isfile(molfile2d)):
                shutil.copyfile(molfile2d,os.path.join(temp_path,'2d','{}-2d.mol'.format(imol)))
    filename=shutil.make_archive(temp_path, 'zip', root_dir=temp_path)
    shutil.move(filename,os.path.join(uploaddir,'scratch','structures-{}.zip'.format(userid)))
    try:
        shutil.rmtree(temp_path)
    except Exception:
        pass
    print 'Location: ../uploads/scratch/structures-{}.zip \n\n'.format(userid)
    sys.exit()

if export == 'csv':
    #Create CSV file with all available data
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    q.execute("""SELECT m.molname, m.molweight, t.nickname, dt.type, d.value, 
                        dt.units, m.molid, m.dateadded 
                     FROM molecules m 
                        LEFT JOIN moldata d ON m.molid=d.molid  
                        LEFT JOIN targets t ON d.targetid=t.targetid 
                        LEFT JOIN datatypes dt ON dt.datatypeid=d.datatype 
                    WHERE m.molid IN %s
                    ORDER BY m.molid
              """,[tuple(molids)])    
    
    csv_file='../public/uploads/scratch/spreadsheet-{}.csv'.format(userid)
    with open(csv_file,'w') as f:
        f.write('Compound,MW,Target,Data type,Value,Units,Molid,Date Added\n')
        for row in q.fetchall():
            for item in row:
                f.write('{},'.format(item))
            f.write('\n')
    q.close()
    dbconn.close() 
    print 'Location: ../uploads/scratch/spreadsheet-{}.csv \n\n'.format(userid)
    sys.exit()

if export == 'xlsx':
    try:
        from collections import OrderedDict
        sys.path.append(os.path.abspath('../extensions/'))
        assert(os.path.exists('../extensions/xlsxwriter'))
        assert(os.path.exists('../extensions/qikprop'))
        assert(os.path.exists('../extensions/qp_parse.py'))
        import xlsxwriter
        from qp_parse import qp_parse
        
        data = OrderedDict() #Holds all data for xlsx file
        nmol = len(molids)
        
        dbconn = psycopg2.connect(config.dsn)
        q = dbconn.cursor()
        #Get relevant targets ordered by number of data points
        q.execute("""SELECT t.nickname FROM moldata d 
                        LEFT JOIN targets t ON t.targetid=d.targetid  
                        WHERE d.molid IN %s and d.datatype in (1,2,3) 
                        GROUP BY t.nickname ORDER BY count(d.value) DESC 
                  """,[tuple(molids)])
        targets = [ row[0] for row in q.fetchall() ]
        ntargets = len(targets)

        #Generate XLSX file
        workbook = xlsxwriter.Workbook('../public/uploads/scratch/table-{}.xlsx'.format(userid),{'strings_to_numbers':True})
        worksheet = workbook.add_worksheet()

        #Cell Formats
        field_format = workbook.add_format({'bold':True, 'align':'center', 'valign':'vcenter', 'color':'#000000','font_size':14,'border':1})
        default_format = workbook.add_format({'border':1, 'align':'center', 'valign':'vcenter'})
        default_format_red = workbook.add_format({'border':1, 'align':'center', 'valign':'vcenter','bg_color':'#FFC7CE'})
        default_format_green = workbook.add_format({'border':1, 'align':'center', 'valign':'vcenter','bg_color':'#C6EFCE'})

        #Conditional formats - Binding data
        for i in xrange(ntargets):
            worksheet.conditional_format(1,2+i,nmol,2+i,{'type':'3_color_scale','min_color':'green','max_color':'#FFC7CE'})
        #Conditional formats - QP properties
        worksheet.conditional_format(1,ntargets+2,nmol,ntargets+2,{'type':'cell','criteria':'>=','value': 500,'format':default_format_red}) #QPMW
        worksheet.conditional_format(1,ntargets+3,nmol,ntargets+3,{'type':'cell','criteria':'>=','value': 4.5,'format':default_format_red}) #QPlogP
        worksheet.conditional_format(1,ntargets+4,nmol,ntargets+4,{'type':'cell','criteria':'<=','value':-5.0,'format':default_format_red}) #QPlogS
        worksheet.conditional_format(1,ntargets+5,nmol,ntargets+5,{'type':'cell','criteria':'<=','value':-5.0,'format':default_format_red}) #QPCIlogS
        worksheet.conditional_format(1,ntargets+6,nmol,ntargets+6,{'type':'cell','criteria':'<=','value':  25,'format':default_format_red}) #QPcaco2
        worksheet.conditional_format(1,ntargets+7,nmol,ntargets+7,{'type':'cell','criteria':'>=','value':   4,'format':default_format_red}) #QPRuleOf5

        #Freeze 1st row
        worksheet.freeze_panes(1,0)
        #Increase width for columns
        worksheet.set_column(0,0,width=40)  #Image column
        worksheet.set_column(1,20,width=20) #Data columns
        fields = ['Compound']
        fields.extend(targets)
        fields.extend(['QPMW','QPlogP','QPlogS','QPCIlogS','QPcaco2','QPRuleOf5','QPstars','Date','molid'])
        #Write field names on top row
        worksheet.set_row(0,height=15)
        for i,item in enumerate(fields):
            worksheet.write(0,1+i,item,field_format)
       
        if len(targets) > 0:
            q.execute("""SELECT m.molid, m.molname, m.dateadded, avg(d.value), t.nickname, dt.type 
                            FROM molecules m 
                                LEFT JOIN moldata d on m.molid=d.molid 
                                LEFT JOIN targets t ON t.targetid=d.targetid 
                                LEFT JOIN datatypes dt ON d.datatype = dt.datatypeid 
                            WHERE m.molid in %s
                                AND (t.nickname in %s OR t.nickname IS NULL)
                            GROUP BY m.molid, m.molname, m.dateadded, t.nickname, dt.type
                            ORDER BY m.molid 
                        """,[tuple(molids),tuple(targets)])
        else:
            q.execute("""SELECT m.molid, m.molname, m.dateadded, avg(d.value), t.nickname, dt.type 
                            FROM molecules m 
                                LEFT JOIN moldata d on m.molid=d.molid 
                                LEFT JOIN targets t ON t.targetid=d.targetid 
                                LEFT JOIN datatypes dt ON d.datatype = dt.datatypeid 
                            WHERE m.molid in %s
                            GROUP BY m.molid, m.molname, m.dateadded, t.nickname, dt.type
                            ORDER BY m.molid 
                        """,[tuple(molids)])

        for row in q.fetchall():
            mid = str(row[0])
            if mid not in data:
                data[mid] = { 'molid':str(row[0]),
                              'Compound':row[1],
                              'Date':row[2].date().isoformat(),
                              row[4]:row[3],
                            }
                if row[3] == 0:
                    data[mid][row[4]] = 'N/A'
                data[mid].update(qp_parse('../public/uploads/qikprop/{}-QP.txt'.format(mid)))
            else:
                if row[3] == 0:
                    data[mid][row[4]] = 'N/A'
                else:
                    data[mid][row[4]] = row[3]

        q.close()
        dbconn.close()

        #Write data
        startpos = [1,1]
        for mol in data:
            #Write image in 1st column
            worksheet.write_blank(startpos[0],0,'',default_format)
            worksheet.insert_image(startpos[0],0,'../public/uploads/sketches/{}.png'.format(data[mol]['molid']),{'x_scale':.6,'y_scale':.6, 'y_offset':1})
            worksheet.set_row(startpos[0],height=95)
            #Write remaining information
            startpos[1]=1
            for item in fields:
                if item not in data[mol]:
                    data[mol][item] = '-'
                worksheet.write(startpos[0],startpos[1],data[mol][item],default_format)
                startpos[1]+=1
            startpos[0]+=1
        
        workbook.close()
        print 'Location: ../uploads/scratch/table-{}.xlsx \n\n'.format(userid) 
        sys.exit()
    except Exception:
        config.returnhome(71)

if export == 'pdf':
    #Generate HTML report, convert to PDF with wktmltopdf
    htmlfile = '../public/uploads/scratch/report-{}.html'.format(userid)
    pdffile  = '../public/uploads/scratch/report-{}.pdf'.format(userid)
    dbconn = psycopg2.connect(config.dsn)
    q = dbconn.cursor()
    q.execute("""WITH molinfo AS 
                    (SELECT m.molid,m.molname,m.molweight,
                            m.molformula,m.dateadded,u.username 
                        FROM molecules m 
                            LEFT JOIN users u ON m.authorid=u.userid 
                        WHERE m.molid in %s
                    ),
                    measurements AS 
                        (SELECT d.molid,d.value,t.type,t.units,r.nickname 
                            FROM moldata d 
                                LEFT JOIN targets r ON r.targetid=d.targetid 
                                LEFT JOIN datatypes t ON t.datatypeid=d.datatype 
                            WHERE d.molid in %s AND t.units!='file'
                        )
                    SELECT * from molinfo mi 
                        LEFT OUTER JOIN measurements mm ON mi.molid=mm.molid 
                        ORDER BY mi.molname
              """,[tuple(molids),tuple(molids)])    
    response=q.fetchall()
    q.close()
    dbconn.close()
    with open(htmlfile,'w') as fout:
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
                    molname=row[1]
                    molweight=row[2]
                    molformula=row[3]
                    dateadded=row[4].strftime('%b %d, %Y %I:%M%p')
                    author=row[5]
                    break
            htmlstr+='<h1>{}</h1>'.format(molname)
            htmlstr+='<br /><br /><div id="infodiv"><table id="infotable">'
            htmlstr+='<tr><td class="infotd">Added by</td><td class="infotd infotdleft">{}</td></tr>'.format(author)
            htmlstr+='<tr><td class="infotd">Added on</td><td class="infotd infotdleft">{}</td></tr>'.format(dateadded)
            htmlstr+='<tr><td class="infotd">MW</td><td class="infotd infotdleft">{}</td></tr>'.format(molweight)
            htmlstr+='<tr><td class="infotd">Formula</td><td class="infotd infotdleft">{}</td></tr>'.format(molformula)
            htmlstr+='<tr><td class="infotd">IUPAC</td><td class="infotd infotdleft"></td></tr>'
            htmlstr+='<tr><td class="infotd">CAS</td><td class="infotd infotdleft"></td></tr>'
            htmlstr+='</table></div>'
            htmlstr+='<img src="../sketches/{}.png" /><br />'.format(mol)
            htmlstr+='<table id="datatable">'
            htmlstr+='''<tr>
                            <th class="datath datathleft">Data</th>
                            <th class="datath datathleft">Value</th>
                            <th class="datath datathleft">Units</th>
                            <th class="datath datathright">Target</th>
                       </tr>
                     '''
            for row in response:
                if str(row[0])==mol:
                    target=row[10]
                    datatype=row[8]
                    value=row[7]
                    units=row[9]
                    if(not value or not datatype or not units):
                        continue
                    if(units):
                        units=units.decode('utf-8').encode('latin-1')
                    if(not target):
                        target='-'
                    htmlstr+='<tr><td class="datatd datatdleft">{}</td>'.format(datatype)
                    htmlstr+='<td class="datatd">{}</td>'.format(value)
                    htmlstr+='<td class="datatd">{}</td>'.format(units)
                    htmlstr+='<td class="datatd datatdright">{}</td></tr>'.format(target)
            htmlstr+='</table>' 

            htmlstr+='<div class="clearfloat"></div>'
            htmlstr+='<div class="pagebreak"><span style="display:none;">&nbsp;</span></div>'        
        htmlstr+="""
                </body>
            </html>
        """
        fout.write(htmlstr)
    subprocess.call([os.path.join(config.wkhtmltopdfdir,'wkhtmltopdf'),htmlfile,pdffile],stdout=open(os.devnull,'w'),stderr=open(os.devnull,'w'))
    print 'Location: ../uploads/scratch/report-{}.pdf \n\n'.format(userid)
    sys.exit()

