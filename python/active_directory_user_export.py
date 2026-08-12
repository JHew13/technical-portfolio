# Active Directory User Export
# Retrieves directory user data and transforms it into a structured output for reporting.
#
# Portfolio note: This public sample is based on code I originally wrote for an
# internal business tool. Proprietary names, endpoints, identifiers, sample data,
# and environment-specific values have been replaced with generic equivalents.
# SANITIZED COPY: credentials, internal server/domain names, database identifiers,
# and Active Directory distinguished names have been replaced with placeholders.
# Original code structure and formatting have otherwise been preserved.

#---List of installs by cmd prompt
#---pip install sqlalchemy
#---pip install numpy
#---pip install pyodbc
#---pip install ldap3
#---pip install pandas

#Official documentations
#---pip (python package manager) documentation: https://pip.pypa.io/en/stable/installing/#id7
#---pandas: https://pandas.pydata.org/
#---sqlalchemy: https://www.sqlalchemy.org/
#---ldap3: https://ldap3.readthedocs.io/
#---numpy: https://numpy.org/
#---pyodbc: https://www.easysoft.com/developer/languages/python/pyodbc.html


#--imports for code to function properly DO NOT DELETE---
import sys
from datetime import datetime
import time
import ldap3
from ldap3 import Server, Connection, ALL, NTLM, ALL_ATTRIBUTES, ALL_OPERATIONAL_ATTRIBUTES, AUTO_BIND_NO_TLS, SUBTREE,BASE,LEVEL, Reader, ObjectDef
from ldap3.core.exceptions import LDAPCursorError
from tzlocal import get_localzone  # tzlocal needs to be extra installed
from collections import OrderedDict
from collections import defaultdict
import dateutil.parser
import pyodbc
import numpy as np
from numpy.random import randn
from pandas import Series, DataFrame
import pandas as pd
from importlib import reload
from sqlalchemy import create_engine
from sqlalchemy.types import TIMESTAMP as typeTIMESTAMP
from sqlalchemy.dialects.postgresql import insert
from sqlalchemy import column
from sqlalchemy.exc import InvalidRequestError
import sys
import csv

import sqlalchemy as sa
reload(sys)
#--sys.setdefaultencoding('utf8')
maxInt = sys.maxsize
datetime.now(tz=None)
#-----------Start of code----------#
#---Set default coding of system---#

#---set pandas options for data---#
pd.set_option('large_repr', 'info')
pd.set_option('display.max_colwidth', 1)


#--set connection string for sqlalchemy
engine = sa.create_engine('mssql+pyodbc://<SQL_SERVER>/<DATABASE>?driver=SQL Server Native Client 11.0')
connection = engine.connect()
metadata = sa.MetaData()

#engine2 = sa.create_engine('mssql+pyodbc://<SQL_SERVER_2>/<DATABASE_2>?driver=SQL Server Native Client 11.0')
#connection2 = engine2.connect()
metadata2 = sa.MetaData()
#get your local timezone
mytz = get_localzone()
#---define server information---
xray_data=[]
innovate_data=[]
carecore_data=[]
list1=[]

def All_AD_USERS():

     csv.field_size_limit(100000000)
     csv.field_size_limit()
     print('processing carecore data...')
        #connection credentials to LDAP server
     server_name_carecore = '<LDAP_SERVER_CARECORE>'
     domain_name = '<DOMAIN>'
     user_name = '<USERNAME>'
     password = '<YOUR_PASSWORD>'

     carecore_server = Server(server_name_carecore, get_info=ALL)
     #---connect to ldap with pre-defined variables---
     carecore_conn = Connection(carecore_server, user='{}\\{}'.format(domain_name, user_name), password=password, authentication=NTLM,auto_bind=True,return_empty_attributes=False)

     carecore_conn_search = carecore_conn.extend.standard.paged_search(search_base='<CARECORE_SEARCH_BASE>',
                                                  search_filter= '(&(objectCategory=user))',
                                                  attributes=['userPrincipalName','employeeID','telephoneNumber','mail','mailNickname','cn','department','extensionAttribute9','primaryGroupID','sn']

                                                  )
          #---For loop to loop through results---
     for entry in carecore_conn_search:
          carecore_data.append(entry['attributes'])
          #---set retrieved list data to pandas dataframe---
     df_carecore = pd.DataFrame(data=carecore_data)

     df_carecore


     print('Processing all XRAY AD-Users under Innovate Server')

     server_name = '<LDAP_SERVER_XRAY_1>'


     server = Server(server_name, get_info=ALL)

     conn = Connection(server, user='{}\\{}'.format(domain_name, user_name), password=password, authentication=NTLM,auto_bind=True,return_empty_attributes=False)
     conn_search = conn.extend.standard.paged_search(
                                                  search_base='<XRAY_SEARCH_BASE>',
                                                  search_filter='(&(objectCategory=user))',
                                                  attributes=['userPrincipalName','employeeID','telephoneNumber','mail','mailNickname','cn','department','extensionAttribute9','primaryGroupID','sn']

                                                   )
     for entry in conn_search:
          xray_data.append(entry['attributes'])


     df_xray = pd.DataFrame(data=xray_data)
     df_xray

     print('xray_data stored!')


#*************************************_________________________________________________________________________*****************************
     server_name_innovate ='<LDAP_SERVER_<DOMAIN>_1>'
     server_innovate = Server(server_name_innovate, get_info=ALL)

     conn_innovate = Connection(server_innovate, user='{}\\{}'.format(domain_name, user_name), password=password, authentication=NTLM,auto_bind=True,return_empty_attributes=False)

     conn_innovate_search = conn_innovate.extend.standard.paged_search(
                                                  search_base='<INNOVATE_SEARCH_BASE>',
                                                  search_filter= '(&(objectClass=user)(objectClass=person)(memberOf=CN=All Employees,<INNOVATE_SEARCH_BASE>))',
                                                  attributes=['userPrincipalName','employeeID','telephoneNumber','mail','mailNickname','cn','department','extensionAttribute9','primaryGroupID','sn'],

                                                   )



     for entry in conn_innovate_search:
          innovate_data.append(entry['attributes'])


     print('...Processing all <DOMAIN> AD-Users under Innovate Server.')
     print('Innovate Data Stored!')

     #____________________________________________________________________store Innovate data in Innovate List Variable____________________________________________________________________
     df_innovate = pd.DataFrame(data=innovate_data)


     #____________________________________________________________________Join data frames for xray and innovate____________________________________________________________________
     #df_ad_users = pd.merge(df_xray,df_innovate,left_on='userPrincipalName')
     df3=df_innovate.append([df_xray,df_carecore])
     print('...Combining Data')

     #____________________________________________________________________save CSV data____________________________________________________________________
     df3.to_csv('tbl_intake_AD_Users.csv', sep=',',header=True,index=False)


     #____________________________________________________________________retrieve CSV data____________________________________________________________________
     df_final = pd.read_csv('tbl_intake_AD_Users.csv',sep=',',engine="python")
     df_final = df_final[pd.notnull(df_final['cn'])]
     

 #string split
     print('...Slicing Necessary Fields')
     
     df_final['windows_login']=df_final.extensionAttribute9 +'/'+ df_final.mailNickname
#string split
     df_final['windows_login']=df_final['windows_login'].str[8:]
     df_final['Domain']=df_final['extensionAttribute9'].str[8:]
     df_final['LoginName']=df_final['mailNickname']
     df_final['DateLoaded']=datetime.now()
     print('...Inserting into DB Intake. Please allow some time for this process to complete.')
     
      
     df_final=df_final[['userPrincipalName','Domain','LoginName','windows_login','employeeID','telephoneNumber','mail','mailNickname','cn','department','extensionAttribute9','primaryGroupID','sn','DateLoaded']]
     df_final
     numOfRows = df_final.shape[0]

     #____________________________________________________________________Send to DB____________________________________________________________________
     df_final.to_sql('tbl_Intake_AD_Users', con=engine, if_exists='replace',index=False)
     #df_final.to_sql('tbl_Intake_AD_Users', con=engine2, if_exists='replace',index=False)

     engine
     print('Successfuly inserted',numOfRows, 'into tbl_Intake_AD_Users')

     return


All_AD_USERS();

     #---define search parameter query for ldap user information---
def innovate_insert():
     #connection credentials to LDAP server
     print('processing innovate data...')
     server_name = '<LDAP_SERVER_<DOMAIN>_2>'
     domain_name = '<DOMAIN>'
     user_name = '<USERNAME>'
     password = '<YOUR_PASSWORD>'
     server = Server(server_name, get_info=ALL)
     #---connect to ldap with pre-defined variables---
     conn = Connection(server, user='{}\\{}'.format(domain_name, user_name), password=password, authentication=NTLM,auto_bind=True,return_empty_attributes=False)
     #---define variables---
     conn_search = conn.extend.standard.paged_search(search_base='<DIRECTORY_SEARCH_BASE>',
                                                  search_filter= '(&(objectClass=user)(objectClass=person))',
                                                  attributes=['givenName','sAMAccountName','employeeID','sAMAccountType','userPrincipalName','title','physicalDeliveryOfficeName','telephoneNumber','homePhone','accountExpires','badPwdCount','cn','codePage','company','countryCode','department','description','displayName','distinguishedName','extensionAttribute1','extensionAttribute10','extensionAttribute15','extensionAttribute8','extensionAttribute9','instanceType','l','legacyExchangeDN','lockoutTime','logonCount','mail','mailNickname','memberOf','mobile','objectSid','postalCode','primaryGroupID','showInAddressBook','sn','st','streetAddress','targetAddress','userAccountControl'],
                                                  )
          #---For loop to loop through results---
     for entry in conn_search:
               list1.append(entry['attributes'])
          #---set retrieved list data to pandas dataframe---
     df = pd.DataFrame(data=list1)
          #---save data to csv file setting properties---
          #---df.to_csv('table_name',seperator)--
     df.to_csv('ad_insert_innovate.csv', sep=',',header=True,index=False)
          #---set df_insert variable to read the saved csv---
     df_insert = pd.read_csv('ad_insert_innovate.csv')
          #---set the index to first column holding data---
     df_insert.set_index('accountExpires')
          #---send the data to sql ***if exists: "append" to add rows or you can use "replace" to drop existing data***---
     df_insert.to_sql('tbl_AD_Users_Innovate', con=engine, if_exists='replace',index=False)

     print ('Succesfully inserted innovate AD data into the specified database!')
     return
#call innovate function
#innovate_insert();
def xray_insert():
     #connection credentials to LDAP server
     print('processing xray data...')
     server_name = '<LDAP_SERVER_XRAY_2>'
     domain_name = '<DOMAIN>'
     user_name = '<USERNAME>'
     password = '<YOUR_PASSWORD>'
     server = Server(server_name, get_info=ALL)
     #---connect to ldap with pre-defined variables---
     conn = Connection(server, user='{}\\{}'.format(domain_name, user_name), password=password, authentication=NTLM,auto_bind=True,return_empty_attributes=False)

     conn_search = conn.extend.standard.paged_search(search_base='<XRAY_OPERATIONS_SEARCH_BASE>',
                                                search_filter= '(&(objectClass=user)(objectClass=person))',
                                                attributes=['givenName','msExchHomeServerName','sAMAccountName','employeeID','sAMAccountType','userPrincipalName','title','physicalDeliveryOfficeName','telephoneNumber','homePhone','accountExpires','badPwdCount','cn','codePage','company','countryCode','department','description','displayName','distinguishedName','extensionAttribute1','extensionAttribute2','extensionAttribute3','extensionAttribute10','extensionAttribute15','extensionAttribute8','extensionAttribute9','instanceType','l','legacyExchangeDN','lockoutTime','logonCount','mail','mailNickname','memberOf','mobile','objectSid','postalCode','primaryGroupID','showInAddressBook','sn','st','streetAddress','targetAddress','userAccountControl'],
                                                )
#---For loop to loop through results---
     for entry in conn_search:
          list1.append(entry['attributes'])
     #---set retrieved list data to pandas dataframe---
     df = pd.DataFrame(data=list1)
     #---save data to csv file setting properties---
     #---df.to_csv('table_name',seperator)--
     df.to_csv('ad_insert_xray.csv', sep=',',header=True,index=False)
     #---set df_insert variable to read the saved csv---
     df_insert = pd.read_csv('ad_insert_xray.csv')
     #---set the index to first column holding data---
     df_insert.set_index('accountExpires')
     #---send the data to sql ***if exists: "append" to add rows or you can use "replace" to drop existing data***---
     df_insert.to_sql('tbl_ad_users_xray', con=engine, if_exists='append', index=False)

     print ('Succesfully inserted xray AD data into the specified database!')
     return
#call xray function
#^^^^^^^^^^^^^^xray_insert()^^^^^^^^^^^^^^^^^^^^^^^^^
def carecore_insert():
     print('processing carecore data...')
        #connection credentials to LDAP server
     server_name_carecore = '<LDAP_SERVER_CARECORE>'

     carecore_server = Server(server_name, get_info=ALL)
     #---connect to ldap with pre-defined variables---
     carecore_conn = Connection(carecore_server, user='{}\\{}'.format(domain_name, user_name), password=password, authentication=NTLM,auto_bind=True,return_empty_attributes=False)

     carecore_conn_search = carecore_conn.extend.standard.paged_search(search_base='<CARECORE_GROUP_SEARCH_BASE>',
                                                  search_filter= '(&(objectClass=user)(objectClass=person)(memberOf=<ALL_EMPLOYEES_GROUP>))',
                                                  attributes=['userPrincipalName','employeeID','telephoneNumber','mail','mailNickname','cn','department','extensionAttribute9','primaryGroupID','sn']

                                                  )
          #---For loop to loop through results---
     for entry in carecore_conn_search:
               list1.append(entry['attributes'])
          #---set retrieved list data to pandas dataframe---
     df = pd.DataFrame(data=list1)
          #---save data to csv file setting properties---
          #---df.to_csv('table_name',seperator)--
     df.to_csv('ad_insert_carecore.csv', sep=',',header=True,index=False)
          #---set df_insert variable to read the saved csv---
     df_insert = pd.read_csv('ad_insert_carecore.csv')
          #---set the index to first column holding data---
     df_insert.set_index('accountExpires')
          #---send the data to sql ***if exists: "append" to add rows or you can use "replace" to drop existing data***---
     df_insert.to_sql('tbl_ad_users_carecore', con=engine, if_exists='replace',index=False)

     print ('Succesfully inserted innovate AD data into the specified database!')
     return
def io_crosstrain():
     print('processing imaged one cross trained agents...')
        #connection credentials to LDAP server
     server_name = '<LDAP_SERVER_<DOMAIN>_2>'
     domain_name = '<DOMAIN>'
     user_name = '<USERNAME>'
     password = '<YOUR_PASSWORD>'
     server = Server(server_name, get_info=ALL)
     #---connect to ldap with pre-defined variables---
     conn = Connection(server, user='{}\\{}'.format(domain_name, user_name), password=password, authentication=NTLM,auto_bind=True,return_empty_attributes=False)


     conn_search = conn.extend.standard.paged_search(search_base='<CROSSTRAIN_SEARCH_BASE>',
                                                  search_filter= '(&(objectClass=user)(objectClass=person)(memberOf=*))',
                                                  attributes='member',
                                                  search_scope='SUBTREE'
                                                  )
          #---For loop to loop through results---
     for entry in conn_search:
               list1.append(entry['raw_dn'])
          #---set retrieved list data to pandas dataframe---
     v=list1[0]
     df = pd.DataFrame(data=list1)
     df.to_csv('ad_insert_IO_CrossTrain.csv', sep=',',header=True,index=False)


          #---save data to csv file setting properties---
          #---df.to_csv('table_name',seperator)--

          #---set df_insert variable to read the saved csv---
     df_insert = pd.read_csv('ad_insert_IO_CrossTrain.csv',sep=',',engine="python",skipfooter=1)
     df_insert.dropna(inplace = True)
     new = df_insert["0"].str.split(",", n = 2, expand = True)

 #string split
     df_insert["Fullname"]= new[0].str[3:]
     df_insert["OU"]= new[1].str[3:]
     df_insert["Server"]= new[2].str[3:]



     df_insert.drop(columns =["0"], inplace = True)

     df_insert

          #---set the index to first column holding data---

          #---send the data to sql ***if exists: "append" to add rows or you can use "replace" to drop existing data***---
     df_insert.to_sql('tbl_ad_image_one_xtrain', con=engine, if_exists='replace',index=False)

     print ('Succesfully inserted image one cross trained agents into the specified database!')
     return
#io_crosstrain();
#call carecore function
#carecore_insert();
def All_Innovate():
     csv.field_size_limit(100000000)
     csv.field_size_limit()
     print('Processing all Innovate AD-Users')
        #connection credentials to LDAP server
     server_name = '<LDAP_SERVER_<DOMAIN>_2>'
     domain_name = '<DOMAIN>'
     user_name = '<USERNAME>'
     password = '<YOUR_PASSWORD>'
     server = Server(server_name, get_info=ALL)
     #---connect to ldap with pre-defined variables---
     conn = Connection(server, user='{}\\{}'.format(domain_name, user_name), password=password, authentication=NTLM,auto_bind=True,return_empty_attributes=False)



     conn_search = conn.extend.standard.paged_search(search_base='<INNOVATE_SEARCH_BASE>',
                                                  search_filter= '(&(objectClass=user)(objectClass=person)(memberOf=CN=All Employees,<INNOVATE_SEARCH_BASE>))',
                                                  attributes=['userPrincipalName','employeeID','telephoneNumber','mail','mailNickname','cn','department','extensionAttribute9','primaryGroupID','sn'],
                                                  search_scope=''
                                                   )
          #---For loop to loop through results---
     for entry in conn_search:
                list1.append(entry['attributes'])
          #---set retrieved list data to pandas dataframe---
     v=list1[0]
     df = pd.DataFrame(data=list1)
     df.to_csv('ad_insert_IO_CrossTrain.csv', sep=',',header=True,index=False)
          #---save data to csv file setting properties---
          #---df.to_csv('table_name',seperator)--
          #---set df_insert variable to read the saved csv---
     df_insert = pd.read_csv('ad_insert_IO_CrossTrain.csv',sep=',',engine="python")
     df_insert.dropna(inplace = True)

 #string split

     df_insert['windows_login']=df.extensionAttribute9 +'/'+ df.mailNickname
#string split
     df_insert['windows_login']=df_insert['windows_login'].str[8:]
     df_insert['Domain']=df_insert['extensionAttribute9'].str[8:]
     df_insert['LoginName']=df_insert['mailNickname']

     df_insert=df_insert[['userPrincipalName','Domain','LoginName','windows_login','employeeID','telephoneNumber','mail','mailNickname','cn','department','extensionAttribute9','primaryGroupID','sn']]
     df_insert.dropna(inplace = True)
     df_insert
          #---set the index to first column holding data--
          #---send the data to sql ***if exists: "append" to add rows or you can use "replace" to drop existing data***---
     df_insert.to_sql('tbl_Intake_AD_Users_Innovate', con=engine, if_exists='append',index=False)

     print ('Succesfully inserted image one cross trained agents into the specified database!')
     return
#All_Innovate();

 #-- run every Tuesday, timestamp
