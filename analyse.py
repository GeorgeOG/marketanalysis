import sys
import quandl
import numpy as np
import datetime

quandl.ApiConfig.api_key = "ZaNCWVQ2Gx8zMKMZwnHn"

# gets name of stock and downloads data
name = sys.argv[1]
data = quandl.get(name, returns='numpy')

# creates file for data, and enters it in the correct format
csvfile = open(name.split('/')[1] + '.csv', 'w+')
prediction_data = []
dates = []
for d in data:
    date = datetime.datetime.strftime(d[0], '%Y-%m-%d')
    date = date[:5] + str(int(date[5:7]) - 1) + date[7:]
    prediction_data.append(int(d[1] * 10000) / 10000.0)
    dates.append(date)


# define moving average function
def ma(l1, l2):

    # initialize averages and list to measure success
    MA1 = prediction_data[0]
    MA2 = prediction_data[0]
    scores = []

    # update moving average for each data entry
    for i in range(1, len(prediction_data) - 1):
        MA1 = MA1 + 2.0 / (l1 + 1) * (prediction_data[i] - MA1)
        MA2 = MA2 + 2.0 / (l2 + 1) * (prediction_data[i] - MA2)

        # record whether prediction was successful
        if i > len(prediction_data) - 100:
            scores.append((np.sign(MA1 - MA2) * np.sign(
                prediction_data[i] - prediction_data[i - 1]) + 1) / 2)

    # print result
    print(np.sign(MA1 - MA2))

    # uncomment to print out success rate
    # print(np.mean(scores))


# define sar function


def sar(alpha):

    # initialize alpha, how short the detectable trends will be
    a = alpha
    a_limit = 0.2

    # is the stock in an uptrend? what is the extreme point in the current uptrend?
    upTrend = (prediction_data[0] < prediction_data[1])
    ep = prediction_data[0]
    sar = prediction_data[0]

    # list to measure success
    scores = []

    # update for each datapoint
    for i in range(1, len(prediction_data) - 1):

        # if the next datapoint is inside the sar value, change the direction of the trend
        if (prediction_data[i] < sar and upTrend) or (prediction_data[i] > sar
                                                      and not upTrend):
            upTrend = not upTrend
            ep = prediction_data[i]
            a = alpha

        # calculate the value of the sar
        sar = sar + a * (ep - sar)

        # if the next datapoint is outside the extreme point, update the extreme point and increase alpha
        if (prediction_data[i] > ep and upTrend) or (prediction_data[i] < ep
                                                     and not upTrend):
            ep = prediction_data[i]

            # don't exceed the limit
            if a < a_limit:
                a += alpha

            # if in an uptrend, re-evaluate the sar
            if prediction_data[i] - sar > 0:
                sar = sar + a * (ep - sar)

        # the next two statements just prevent the sar from moving outside the value too easily
        if (prediction_data[i] < sar and upTrend) or (prediction_data[i] > sar
                                                      and not upTrend):
            sar = prediction_data[i]
        elif (prediction_data[i - 1] < sar
              and upTrend) or (prediction_data[i - 1] > sar and not upTrend):
            sar = prediction_data[i - 1]

        # record whether the prediction was successful
        if i > len(prediction_data) - 100:
            scores.append((np.sign(prediction_data[i] - sar) * np.sign(
                prediction_data[i] - prediction_data[i - 1]) + 1) / 2)

        csvfile.write(dates[i] + ', ' + str(prediction_data[i]) + ', ' +
                      str(sar) + '\n')

    # print out the prediction
    print(np.sign(prediction_data[i] - sar), ',',
          int(np.mean(scores) * 1000) / 10)


# run the sar function with the given value of alpha
sar(0.05)
csvfile.close()
