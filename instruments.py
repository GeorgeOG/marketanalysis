import os
import MySQLdb

# calls API to get data, renames the folder, and deletes the ZIP
os.system(
    '''wget https://www.quandl.com/api/v3/databases/LSE/codes?
    api_key=ZaNCWVQ2Gx8zMKMZwnHn'''
)
os.system('unzip codes')
os.system('mv LSE-datasets-codes.csv instruments.csv')
os.system('rm codes')

# connect to the database
db = MySQLdb.connect(
    host="localhost",
    user="georgegarber",
    passwd="password",
    db="marketanalysisdb")

cur = db.cursor()

# create the instruments table
query = '''create table if not exists instruments(instrumentid varchar(8)
 primary key, description varchar(30), currency varchar(3));'''
cur.execute(query)

instruments = open("instruments.csv", "r")

# go through the list of downloaded instruments
for line in instruments:

    # remove double quotes
    line = line.replace('"', '')

    # makes sure it has a price, and it has a currency
    if line.find('price') == -1 or line.find('Currency') == -1:
        continue

    # extracts the useful information from the description
    instrumentid = line[0:line.find(',')]
    description = line[line.find(',') + 1:line.find('price') + 5]
    currency = line[line.find('Currency ') + 9:line.find('Currency ') + 12]

    # insert the data into the database
    query = '''insert ignore into instruments values
     ("''' + instrumentid + '","' + description + '","' + currency + '");'
    cur.execute(query)

db.commit()
db.close()
