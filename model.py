from keras.models import Sequential
from keras.layers import Dense
import numpy
import quandl
import h5py

quandl.ApiConfig.api_key = 'ZaNCWVQ2Gx8zMKMZwnHn'
numpy.random.seed(69)
barc_data = quandl.get('LSE/BARC', returns='numpy')
test_data = []
for d in barc_data:
    test_data.append(int(d[1]*10000)/10000.0)

X=[]
Y=[]
for d in range(len(test_data)-25):
    set=[]
    for i in range(24):
        set.append(test_data[d+i])
    X.append(set)
    if int(test_data[d+25])>int(test_data[d+24]):
        Y.append([1])
    elif int(test_data[d+25])==int(test_data[d+24]):
        Y.append([0.5])
    else:
        Y.append([0])

model=Sequential()
model.add(Dense(30, input_dim=24, activation='relu'))
model.add(Dense(32, activation='relu'))
model.add(Dense(22, activation='relu'))
model.add(Dense(10, activation='relu'))
model.add(Dense(1, activation='sigmoid'))
model.compile(loss='binary_crossentropy', optimizer='adam', metrics=['accuracy'])
model.fit(X, Y, nb_epoch=100, batch_size=5)
model.save('model.h5')
