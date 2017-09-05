import os
import MySQLdb

os.system('wget https://www.quandl.com/api/v3/databases/LSE/codes?api_key=ZaNCWVQ2Gx8zMKMZwnHn')
os.system('unzip codes')
os.system('mv LSE-datasets-codes.csv instruments.csv')
os.system('rm codes')

db = MySQLdb.connect(host="localhost",
                     user="georgegarber",
                     passwd="password",
                     db="marketanalysisdb")

cur = db.cursor()
query = "create table if not exists instruments(instrumentid varchar(8) primary key, description varchar(30), currency varchar(3));"
cur.execute(query)

instruments = open("instruments.csv", "r")
for line in instruments:
    line = line.replace('"','')
    if line.find('price')==-1 or line.find('Currency')==-1:
        continue
    instrumentid = line[0:line.find(',')]
    description = line[line.find(',')+1:line.find('price')+5]
    currency = line[line.find('Currency ')+9:line.find('Currency ')+12]
    query = 'insert ignore into instruments values ("'+instrumentid+'","'+description+'","'+currency+'");'
    cur.execute(query)

db.commit()
db.close()
