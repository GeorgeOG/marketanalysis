import sys
import quandl
import numpy as np
import datetime

quandl.ApiConfig.api_key = "ZaNCWVQ2Gx8zMKMZwnHn"

name = sys.argv[1]
data = quandl.get(name, returns='numpy')
csvfile = open(name.split('/')[1] + '.csv', 'w+')
prediction_data = []
for d in data:
    csvfile.write(datetime.datetime.strftime(
        d[0], '%Y-%m-%d') + ', ' + str(int(d[1] * 10000) / 10000.0) + '\n')
    prediction_data.append(int(d[1] * 10000) / 10000.0)
csvfile.close()


def ma(l1,l2):

    MA1 = prediction_data[0]
    MA2 = prediction_data[0]

    scores = []

    for i in range(1, len(prediction_data) - 1):
        MA1 = MA1 + 2.0 / (l1 + 1) * (prediction_data[i] - MA1)
        MA2 = MA2 + 2.0 / (l2 + 1) * (prediction_data[i] - MA2)
        if i > len(prediction_data) - 100:
            scores.append((np.sign(MA1 - MA2) *
                          np.sign(prediction_data[i] - prediction_data[i - 1])+1)/2)
    print(np.sign(MA1 - MA2))
    #print(np.mean(scores))


def sar(alpha):
    a = alpha
    a_limit = 0.2
    upTrend = (prediction_data[0] < prediction_data[1])
    ep = prediction_data[0]
    sar = prediction_data[0]
    scores = []

    for i in range(1, len(prediction_data) - 1):
        if (prediction_data[i] < sar and upTrend) or (prediction_data[i] > sar and not upTrend):
            upTrend = not upTrend
            ep = prediction_data[i]
            a = alpha

        sar = sar + a * (ep - sar)

        if (prediction_data[i] > ep and upTrend) or (prediction_data[i] < ep and not upTrend):
            ep = prediction_data[i]
            if a < a_limit:
                a += alpha
            if prediction_data[i]-sar > 0:
                sar = sar + a * (ep - sar)

        if (prediction_data[i] < sar and upTrend) or (prediction_data[i] > sar and not upTrend):
            sar = prediction_data[i]

        elif (prediction_data[i - 1] < sar and upTrend) or (prediction_data[i - 1] > sar and not upTrend):
            sar = prediction_data[i - 1]

        if i > len(prediction_data) - 100:
            scores.append((np.sign(prediction_data[i] - sar) *
                          np.sign(prediction_data[i] - prediction_data[i - 1])+1)/2)

    print(np.sign(prediction_data[i] - sar))
    #print(np.mean(scores))

sar(0.045)
