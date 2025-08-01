import pandas as pd
import numpy as np
from pycaret.classification import setup, create_model, save_model
from featureExtractor import featureExtraction

# Load your datasets
safe_df = pd.read_csv('safe.csv')
safe_df['is_phishing'] = 0
phishing_df = pd.read_csv('phishing.csv')
phishing_df['is_phishing'] = 1

df = pd.concat([safe_df, phishing_df], ignore_index=True)

print(f"Total URLs loaded: {len(df)}")
print(f"Safe URLs: {len(safe_df)}")
print(f"Phishing URLs: {len(phishing_df)}")

# Extract features using the new feature extraction
features = []
for url in df['url']:
    row = featureExtraction(url)
    features.append(row.iloc[0])

X = pd.DataFrame(features)
y = df['is_phishing']

# Combine features and target
data = X.copy()
data['is_phishing'] = y.values

# Setup PyCaret
clf1 = setup(data=data, target='is_phishing', silent=True, session_id=42, html=False, verbose=False)

# Create and save the model
model = create_model('rf')
save_model(model, 'model/phishingdetection')
print("Model trained and saved as model/phishingdetection") 