import os
import MySQLdb

os.system('wget https://www.quandl.com/api/v3/databases/LSE/codes')
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
    if line.find('price')==-1:
        continue
    instrumentid = line[0:line.find(',')]
    description = line[line.find(',')+2:line.find('price')+5]
    currency = line[-5:-2]
    query = 'insert ignore into instruments values ("'+instrumentid+'","'+description+'","'+currency+'");'
    cur.execute(query)

db.commit()
db.close()
