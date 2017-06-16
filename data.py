import sys
import quandl
import numpy
import math

a=10
b=2
c=1.5

#name = sys.argv[0]
name = 'LSE/BARC'
data = quandl.get(name, returns='numpy')
csvfile = open(name.split('/')[1]+'.csv','w+')
prediction_data =[]
for d in data:
    csvfile.write(str(int(d[1]*10000)/10000.0)+'\n')
    prediction_data.append(int(d[1]*10000)/10000.0)
csvfile.close()

size = 25
prediction_data = prediction_data[-size:]
differences=[]
for i in range(size-1):
   d = prediction_data[i+1]-prediction_data[i]
   differences.append(int(d*100)/100.0)

avg_dif = numpy.mean(differences)
std = numpy.std(differences)
spread = avg_dif * std

def sigmoid(x):
  return 1 / (1 + math.exp(-x))

def rtm(dataset):
    days=12
    avg_value = numpy.mean(dataset[-days-1:-1])
    final = dataset[-1:]
    prediction= (avg_value-final)/avg_value
    return sigmoid(a*prediction)

def ma(dataset):
    days=7
    mov_avgs=[]
    for d in range(days,0,-1):
        mov_avg= numpy.mean(dataset[-d-days:-d])
        mov_avgs.append(mov_avg)
    mov_avgs.append(numpy.mean(dataset[-days:]))
    prediction = 0
    for i in range(days, 0, -1):
        prev_diff = mov_avgs[i-1]-dataset[-days+i-2]
        curr_diff = mov_avgs[i]-dataset[-days+i-1]
        if prev_diff*curr_diff < 0:
            prediction = 1.0/i
            if curr_diff > 0:
                prediction *= -1
            break
    return sigmoid(b*prediction)

if abs(spread) > c:
    pred = ma(prediction_data)
else:
    pred = rtm(prediction_data)

print(pred)
