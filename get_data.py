import sys
import quandl

#name = sys.argv[0]
name = 'LSE/BARC'
data = quandl.get(name, returns='numpy')
csvfile = open(name.split('/')[1]+'.csv','w+')

for d in data:
    csvfile.write(str(int(d[1]*10000)/10000.0)+'\n')

X=[]
for i in range(len(data)-24,len(data)):
    X.append(data[i][1])
    
csvfile.close()
