import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
import joblib
import re

def extract_features(url):
    features = {
        'url_length': len(url),
        'domain_length': len(re.findall(r'://([^/]+)', url)[0]) if re.findall(r'://([^/]+)', url) else 0,
        'path_length': len(re.findall(r'://[^/]+(.*)', url)[0]) if re.findall(r'://[^/]+(.*)', url) else 0,
        'dots_in_domain': len(re.findall(r'\.', re.findall(r'://([^/]+)', url)[0]) if re.findall(r'://([^/]+)', url) else ''),
        'contains_ip': 1 if re.match(r'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}', url) else 0,
        'contains_at': 1 if '@' in url else 0,
        'uses_https': 1 if 'https://' in url else 0,
        'multiple_subdomains': 1 if len(re.findall(r'\.', re.findall(r'://([^/]+)', url)[0]) if re.findall(r'://([^/]+)', url) else '') > 2 else 0,
        'contains_hex': 1 if re.match(r'[0-9a-f]{8,}', url) else 0,
        'contains_numbers': 1 if re.match(r'\d', url) else 0,
        'contains_special_chars': 1 if re.match(r'[^a-zA-Z0-9\-\._~:\/?#\[\]@!$&\'()*+,;=]', url) else 0,
        'contains_random_string': 1 if re.match(r'[a-z0-9]{10,}', url) else 0
    }
    return features

def train_model():
    try:
        # Load safe URLs
        safe_df = pd.read_csv('safe.csv')
        safe_df['is_phishing'] = 0  # 0 for safe/legit URLs
        
        # Load phishing URLs
        phishing_df = pd.read_csv('phishing.csv')
        phishing_df['is_phishing'] = 1  # 1 for phishing URLs
        
        # Combine datasets
        df = pd.concat([safe_df, phishing_df], ignore_index=True)
        
        print(f"Total URLs loaded: {len(df)}")
        print(f"Safe URLs: {len(safe_df)}")
        print(f"Phishing URLs: {len(phishing_df)}")
        
        # Extract features from URLs
        X = []
        y = df['is_phishing'].values
        
        print("Extracting features from URLs...")
        for url in df['url']:
            features = extract_features(url)
            X.append(list(features.values()))
        
        X = np.array(X)
        
        # Split the dataset
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
        
        print("\nTraining model...")
        # Train the model
        model = RandomForestClassifier(n_estimators=100, random_state=42)
        model.fit(X_train, y_train)
        
        # Evaluate the model
        train_score = model.score(X_train, y_train)
        test_score = model.score(X_test, y_test)
        print(f"\nModel Performance:")
        print(f"Training accuracy: {train_score:.4f}")
        print(f"Testing accuracy: {test_score:.4f}")
        
        # Feature importance
        feature_names = list(extract_features('http://example.com').keys())
        importances = model.feature_importances_
        feature_importance = dict(zip(feature_names, importances))
        
        print("\nFeature Importance:")
        for feature, importance in sorted(feature_importance.items(), key=lambda x: x[1], reverse=True):
            print(f"{feature}: {importance:.4f}")
        
        # Save the model
        joblib.dump(model, 'phishing_model.joblib')
        print("\nModel saved successfully as 'phishing_model.joblib'")
        
    except Exception as e:
        print(f"Error during training: {str(e)}")

if __name__ == "__main__":
    train_model() 