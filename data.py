import sys
import quandl
import numpy as np

name = sys.argv[1]
data = quandl.get(name, returns='numpy')
csvfile = open(name.split('/')[1] + '.csv', 'w+')
prediction_data = []
for d in data:
    csvfile.write(str(int(d[1] * 10000) / 10000.0) + '\n')
    prediction_data.append(int(d[1] * 10000) / 10000.0)
csvfile.close()

l1 = 15
l2 = 50

MA1 = prediction_data[0]
MA2 = prediction_data[0]

scores = []

for i in range(1, len(prediction_data)-1):
    MA1 = MA1 + 2.0 / (l1 + 1) * (prediction_data[i] - MA1)
    MA2 = MA2 + 2.0 / (l2 + 1) * (prediction_data[i] - MA2)
    if i>len(prediction_data)-100:
        scores.append(np.sign(MA1 - MA2)*np.sign(prediction_data[i]-prediction_data[i-1]))
print(np.sign(MA1 - MA2))
print(np.mean(scores))
