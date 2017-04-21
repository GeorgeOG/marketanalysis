import os
os.system('wget https://www.quandl.com/api/v3/databases/LSE/codes')
os.system('unzip codes')
os.system('mv LSE-datasets-codes.csv instruments.csv')
os.system('rm codes')
