# Sentence Similarity Analysis
# Compares workflow questions to identify exact, partial, and reordered text similarities.
#
# Portfolio note:
# This is a sanitized public sample based on code I originally wrote for an internal
# business tool. Proprietary names, credentials, endpoints, identifiers, and business-
# specific values have been replaced with generic equivalents.
#
# Key concepts demonstrated:
# - SequenceMatcher-based similarity analysis
# - permutations/combinations
# - pandas data preparation
# - duplicate and near-duplicate detection

from difflib import SequenceMatcher
from re import L
import pandas as pd
import itertools 

from itertools import permutations,product,zip_longest
import sqlalchemy as sa
from sqlalchemy import inspect 
from openpyxl import load_workbook

mapping ={}
wb = load_workbook("INPUT_WORKBOOK.xlsx")
ws = wb["Questions + HP"]
for entry, data_boundary in ws.tables.items():
    data = ws[data_boundary]
    content = [[cell.value for cell in ent]
    for ent in data ]

header = content[0]
rest = content[1:]
df = pd.DataFrame(rest,columns = header)
    
Table = mapping[entry]=df

healthPlans = []
#print (Table)
engine = sa.create_engine('mssql+pyodbc://SERVER_NAME/DATABASE_NAME?driver=SQL Server Native Client 11.0')

# In future would like to have this auto populate list with HP
# #df = pd.read_csv("HP_questions_python.csv", engine ='python')
 
#correctedSentence = df.values.tolist()
#prodcutDf = pd.DataFrame(correctedSentence(list(IT.product(*correctedSentence)),columns=['index','hp','question']))
filteredQuestion = list(filter(None, Table["Questions"])) # Isolates the questions from the other columns for easy access
questions = [
    'Health Plan A, Is there a reason for selecting an out-of-network site?',
    'Health Plan A, Would you like assistance finding an in-network provider?',
    'Health Plan B, Has the required documentation been obtained?',
    'Health Plan B, Would you like to continue with the selected provider?',
    'Health Plan C, Is there a reason for selecting an out-of-network lab?',
    'Health Plan C, Would you like to search for an in-network location?',
]
filteredHP = list(filter(None, Table["HP"]))# Isolates the HP from the other columns for easy access
com = [',']
filtered = filteredHP + filteredQuestion

for f in filteredQuestion:
    filtered = filteredHP.append(filteredQuestion)
#print (filtered)
a = []
b =  []
s = []
e = []
originalList = []
ListCompared = []
hp = []
hpCompared =[]
question=[]
QuestionCompared = []
d = []
data = []
l=[]
percent=[]
test2=[]
#res = [v for v in zip_longest(*[k.split(', ') for k in questions])] this does something..


for a,b in permutations(questions,2):
    a = a.split(',')
    hp = a[0]
    question = a[1]
    my_list = list(hp)
    my_list.append(question)
    b = b.split(',')
    hpCompared = b[0]
    QuestionCompared = b[1]
    
    l = SequenceMatcher(None,question,QuestionCompared).ratio() 
    
    
    #print (s)
        
    #originalList.append(questions)
    
        
 

            
        

        #e = s * 100
       
        #c = a,b,e

    data.append({"HP":hp,
                "Questions":question,
                "ComparedHP":hpCompared,
                "QuestionCompared":QuestionCompared,
                "percentage":l})
           
        

 


#print (l2)


#print (l3)
#print (l4)
 
#l1, l2 = [x.split(',')[0] for x in originalList], [x.split(',')[1]for x in originalList]
            

#for x in l2:
    #s = SequenceMatcher(None,l2,b).ratio()

#data = {
        #"HP":hp,      
        #"Question":question,  
        #"HPCompared":hpCompared,
        #"QuestionCompared":s}
        


""" Remove the " to allow filter for sentences that match >80
        if (e >= 80):
            #print (c)
            data={"Health Plan":healthPlan,
                "Sentence":a,  
                "Sentnece Compared":b,
                "Percent Match":e}
            df1 = pd.DataFrame.from_dict(data, orient="index")
            df1 = df1.transpose()  
        else :
            data={"Health Plan":healthPlan,
                "Sentence":"",  
                "Sentnece Compared":"",
                "Percent Match":""}  
 """
 
df1 = pd.DataFrame.from_dict(data)       

df1.to_sql('tbl_Sentence_Compare', con=engine, if_exists='replace', index=False)
 

