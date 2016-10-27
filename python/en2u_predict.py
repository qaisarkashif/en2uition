import numpy as np
import pickle

# Number of options for level 1
w=2197

# Load the model for 'Me'
inputf = open('./trained_en2u_model_1.pkl', 'rb')
model_1 = pickle.load(inputf)
inputf.close()

# Load the model for 'My partner'
inputf = open('./trained_en2u_model_2.pkl', 'rb')
model_2 = pickle.load(inputf)
inputf.close()

# Load data and reshape
m=np.loadtxt('./single_row.txt')
M=m.reshape(m.size/w, w)

# Calculating probabilities 
P1=model_1.decision_function(M)   # for 'Me'
P2=model_2.decision_function(M)   # for 'My partner'

# Normalize and predict
mmin=-1.38916168177
mmax=1.26758577548
dist=np.abs(mmin)+np.abs(mmax)

# Probability for 'Me'
P1=np.abs(P1-mmin)/(dist)*100    
P1=int(P1)

# Probability for 'My partner'
P2=np.abs(P2-mmin)/(dist)*100    
P2=int(P2)

print P1, '\n'
print P2


