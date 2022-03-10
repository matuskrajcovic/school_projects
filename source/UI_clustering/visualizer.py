from array import *
import matplotlib.pyplot as plt
import matplotlib.colors as cls
import random

x = []
y = []
x_c = []
y_c = []

file = open("output.txt")
centroids = int(file.readline())

for i in range(centroids):
 count = int(file.readline())
 line = file.readline()
 s = line.split(' ')
 x_c.append(int(float(s[0])))
 y_c.append(int(float(s[1])))
 x.append([])
 y.append([])
 
 for j in range(count):
  line = file.readline()
  s = line.split(' ')
  x[i].append(int(s[0]))
  y[i].append(int(s[1]))
  
  
for i in range(centroids):
 plt.plot(x[i], y[i], "o", markersize=1)
 plt.plot(x_c[i], y_c[i], "o", color="black", markersize=10, fillstyle="none")
plt.show()