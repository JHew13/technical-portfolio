# Ruleset File-to-Database ETL
# Reads ruleset source files, transforms them with pandas, and loads refreshed tables into SQL Server.
#
# Portfolio note:
# This is a sanitized public sample based on code I originally wrote for an internal
# business tool. Proprietary names, credentials, endpoints, identifiers, and business-
# specific values have been replaced with generic equivalents.
#
# Key concepts demonstrated:
# - ETL-style file ingestion
# - CSV/Excel processing
# - pandas transformation
# - SQLAlchemy database loading

import os
import pandas as pd
import sqlalchemy as sa
import glob

 
def import_ruleset_files():
   
    engine = sa.create_engine('mssql+pyodbc://SQL_SERVER_NAME/RULESET_DATABASE?driver=SQL Server Native Client 11.0')
    pd.set_option('large_repr', 'info')
    pd.set_option('display.max_colwidth', 1)


    
   # path=('M:\\Program Ops Support Services\\Business Pathways\\Rulesets')
    #listOfFiles = glob.glob(path + "/*.csv")
    #print (listOfFiles)
    directory = (r'C:\example\data\rulesets')
    listOfFiles = os.listdir(directory)
    li=[]
    print ("moving rulesets, please wait")
    #what_type =  input('what type of file are you looking for?\n You can use * as wildcard alone or you can list a file type like this *.docx\n')
    for  root, dirs, filenames  in os.walk(directory):    
        for file in filenames:
            file_path = os.path.join(root, file)
            if "Archived Rulesets" in dirs:
                dirs.remove("Archived Rulesets")
                
            if "Archive" in dirs:
                dirs.remove("Archive")
                
            if "Archives" in dirs:
                dirs.remove("Archives")
            
            
            # If it is a CSV I read it with pandas, otherwise I handle the Excel file separately below.
    if file_path.endswith('.csv'):  

                  
            
                df = pd.read_csv(file_path, encoding = 'unicode_escape',engine='python')

              

                

            
                li.append(df)
                split_file_name=file_path.split('\\')
                tablename=split_file_name
                #print (tablename)
                #csv_split=tablename[1].split('.csv')
                
                
                head_tail=  os.path.split(file_path)
                table_name_complete='tbl_'+head_tail[1]
                
                #print(head_tail[1])
                #print('I am working')
                #print(table_name_complete)
               #print (table_name_complete)
                

        
              #  fout = open(table_name_complete,'w')
                
               # reader = csv.reader(fin, delimiter=',', quotechar='"',lineterminator='\n')
                #writer = csv.writer(fout, delimiter=',', quotechar='"',lineterminator='\n')
               # firstrow = True
               # for row in reader:
                #    if firstrow:
                 #       row.append('UUID')
                  #      firstrow = False
                   # else:
                    #    row.append(uuid.uuid3(uuid.NAMESPACE_URL, file_path))
                     #   row.append(head_tail[1])
                      #  writer.writerow(row)
                    
                    #print (writer)
                # This takes the DataFrame I created above and loads it into SQL Server.
    # I used replace here because the goal was to refresh the table with the latest version of the ruleset file.
    df.to_sql((table_name_complete), con=engine, if_exists='replace',index=False)
                    #print(li)


        
    
#Other Ruleset imports any CSV in a userdefined directory. 
#The table name will be whatever the file name is. 
   
    
def other_ruleset():
    typefile = ()
    typefollow =()
    engine = sa.create_engine('mssql+pyodbc://SQL_SERVER_NAME/IMPORT_DATABASE?driver=SQL Server Native Client 11.0')
    connection = engine.connect()
    metadata = sa.MetaData()
    pd.set_option('large_repr', 'info')
    pd.set_option('display.max_colwidth', -1)


    user_defined_path = input('Please type out the path where the files are located.')

    path=user_defined_path
    file_type = input('please state the file type.\n')
    listOfFiles = glob.glob(path + "/*"+file_type)
    if file_type == "csv":
        typefile=pd.read_csv
        typefollow= engine=='python'

        print ('Searching for'+" "+file_type) 
    else :
        typefile=pd.read_excel
        typefollow=sheet_name=None

        print ('Searching for'+" "+file_type)

    li=[]

#what_type =  input('what type of file are you looking for?\n You can use * as wildcard alone or you can list a file type like this *.docx\n')
    
    for filename in listOfFiles:
     
        df = typefile(filename,typefollow)

    

            
        li.append(df)
        split_file_name=filename.split('\\')
        tablename=split_file_name
        csv_split=tablename[4].split(file_type)

        table_name_complete='tbl_'+csv_split[0]

    #print('I am working')
    #print(table_name_complete)
        
    df.to_sql((table_name_complete), con=engine, if_exists='replace',index=False)
    print('I work')

         

    
    
        
def table_transfer():

         
    df_personal_codes=[]

     # personal_connect

    personal_engine = sa.create_engine('mssql+pyodbc://SQL_SERVER_NAME/IMPORT_DATABASE?driver=SQL Server Native Client 11.0')
    connection1 = personal_engine.connect()
    metadata1 = sa.MetaData()

    csv_tables1 = sa.Table('tbl_Diagnostic Ultrasound Codes',metadata1, autoload=True, autoload_with=personal_engine)
   
    query1 = sa.select([csv_tables1])

    ResultProxy1 = connection1.execute(query1)
    ResultSet1 = ResultProxy1.fetchall()
    df_personal = pd.DataFrame(data=ResultSet1[:10], columns=[csv_tables1.columns.keys()])
    p_keys= csv_tables1.columns.keys()
    p_key=p_keys[0]
    print (p_key)
     # Repdb connect -------------------------------------------------------------------------------------


    engine = sa.create_engine('mssql+pyodbc://REPORTING_SQL_SERVER/REPORTING_DATABASE?driver=SQL Server Native Client 11.0')
    connection = engine.connect()
    metadata = sa.MetaData()

    #Equivalent to 'SELECT * FROM tblCPTCompany'-------------------------------------------------------------------------
    cpt_company = sa.Table('tblCPTCompany',metadata, autoload=True, autoload_with=engine)

    query = sa.select([cpt_company])
    ResultProxy = connection.execute(query)
    ResultSet = ResultProxy.fetchall()
    df = pd.DataFrame(data=ResultSet[:10], columns=[cpt_company.columns.keys()])
    df2 = pd.DataFrame (df[1], columns=['CPTCode'])
    print (df2)
    results = df.loc[df[2] == 76506]
    print (results)
  

#other_ruleset(); #manual file search and upload
import_ruleset_files();     #auto file search and upload of whole directory    
#table_transfer(); import_ruleset_files();    

