#!/usr/bin/env python3
"""
Advanced Phishing Detection Model Trainer
Trains a high-accuracy machine learning model for URL phishing detection
"""

import pandas as pd
import numpy as np
import re
import joblib
import warnings
from urllib.parse import urlparse, parse_qs
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.model_selection import train_test_split, cross_val_score, GridSearchCV
from sklearn.metrics import classification_report, confusion_matrix, accuracy_score, precision_score, recall_score, f1_score
from sklearn.preprocessing import StandardScaler
from sklearn.feature_selection import SelectKBest, f_classif
import matplotlib.pyplot as plt
import seaborn as sns
from datetime import datetime
import json
import os

warnings.filterwarnings('ignore')

class PhishingDetectorTrainer:
    def __init__(self):
        self.model = None
        self.scaler = StandardScaler()
        self.feature_selector = None
        self.feature_names = []
        self.model_info = {}
        
    def extract_features(self, url):
        """Extract comprehensive features from URL"""
        try:
            parsed = urlparse(url)
            
            # Basic URL features
            url_length = len(url)
            domain_length = len(parsed.netloc)
            path_length = len(parsed.path)
            query_length = len(parsed.query)
            
            # Domain analysis
            dots_in_domain = parsed.netloc.count('.')
            contains_ip = bool(re.match(r'^(\d{1,3}\.){3}\d{1,3}$', parsed.netloc))
            contains_at = '@' in url
            uses_https = parsed.scheme == 'https'
            
            # Subdomain analysis
            subdomains = parsed.netloc.split('.')
            has_multiple_subdomains = len(subdomains) > 2
            
            # Character analysis
            contains_hex = bool(re.search(r'[0-9a-fA-F]{8,}', url))
            contains_numbers = bool(re.search(r'\d', parsed.netloc))
            contains_special_chars = bool(re.search(r'[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]', url))
            
            # Path depth
            url_depth = parsed.path.count('/') - 1 if parsed.path else 0
            
            # Random string detection
            contains_random_string = bool(re.search(r'[a-zA-Z0-9]{10,}', url))
            
            # TLD analysis
            suspicious_tlds = ['.tk', '.ml', '.ga', '.cf', '.gq', '.xyz', '.top', '.club', '.site', '.online']
            suspicious_tld = any(tld in parsed.netloc for tld in suspicious_tlds)
            
            # Brand name detection (common legitimate brands)
            legitimate_brands = ['google', 'facebook', 'youtube', 'amazon', 'microsoft', 'apple', 'netflix', 'twitter', 'instagram', 'linkedin']
            contains_brand_name = any(brand in parsed.netloc.lower() for brand in legitimate_brands)
            brand_name_count = sum(1 for brand in legitimate_brands if brand in parsed.netloc.lower())
            
            # Suspicious words detection
            suspicious_words = ['login', 'signin', 'verify', 'secure', 'account', 'banking', 'paypal', 'ebay', 'amazon', 'update', 'confirm', 'verify', 'security', 'login', 'signin', 'verify', 'secure', 'account', 'banking', 'paypal', 'ebay', 'amazon', 'update', 'confirm', 'verify', 'security']
            suspicious_word_count = sum(1 for word in suspicious_words if word in url.lower())
            
            # Entropy calculation for randomness
            def calculate_entropy(text):
                if not text:
                    return 0
                prob = [float(text.count(c)) / len(text) for c in set(text)]
                entropy = -sum(p * np.log2(p) for p in prob)
                return entropy
            
            entropy_score = calculate_entropy(parsed.netloc)
            
            # Additional advanced features
            has_redirect = 'redirect' in url.lower() or 'goto' in url.lower()
            has_shortener = any(shortener in url.lower() for shortener in ['bit.ly', 'goo.gl', 'tinyurl', 't.co'])
            has_typosquatting = self._detect_typosquatting(parsed.netloc)
            has_homograph = self._detect_homograph(parsed.netloc)
            
            features = [
                url_length, domain_length, path_length, query_length,
                dots_in_domain, int(contains_ip), int(contains_at), int(uses_https),
                int(has_multiple_subdomains), int(contains_hex), int(contains_numbers),
                int(contains_special_chars), url_depth, int(contains_random_string),
                int(suspicious_tld), int(contains_brand_name), brand_name_count,
                suspicious_word_count, entropy_score, int(has_redirect),
                int(has_shortener), int(has_typosquatting), int(has_homograph)
            ]
            
            return features
            
        except Exception as e:
            # Return default features if parsing fails
            return [0] * 23
    
    def _detect_typosquatting(self, domain):
        """Detect potential typosquatting attacks"""
        common_typos = {
            'google': ['g00gle', 'go0gle', 'g0ogle', 'goog1e', 'g00g1e'],
            'facebook': ['faceb00k', 'faceb0ok', 'facebo0k', 'faceb00k'],
            'amazon': ['amaz0n', 'amaz0n', 'amaz0n', 'amaz0n'],
            'paypal': ['paypa1', 'paypa1', 'paypa1', 'paypa1'],
            'ebay': ['ebay', 'ebay', 'ebay', 'ebay']
        }
        
        for brand, typos in common_typos.items():
            if brand in domain.lower():
                for typo in typos:
                    if typo in domain.lower():
                        return True
        return False
    
    def _detect_homograph(self, domain):
        """Detect potential homograph attacks using similar characters"""
        homograph_chars = {
            'a': ['а', 'а', 'а', 'а'],  # Cyrillic 'а'
            'e': ['е', 'е', 'е', 'е'],  # Cyrillic 'е'
            'o': ['о', 'о', 'о', 'о'],  # Cyrillic 'о'
            'c': ['с', 'с', 'с', 'с'],  # Cyrillic 'с'
            'p': ['р', 'р', 'р', 'р'],  # Cyrillic 'р'
            'x': ['х', 'х', 'х', 'х'],  # Cyrillic 'х'
            'y': ['у', 'у', 'у', 'у']   # Cyrillic 'у'
        }
        
        for latin, cyrillic in homograph_chars.items():
            if any(char in domain for char in cyrillic):
                return True
        return False
    
    def load_data(self, phishing_file, safe_file):
        """Load and preprocess training data"""
        print("Loading training data...")
        
        # Load phishing URLs
        phishing_df = pd.read_csv(phishing_file)
        # Handle different column names
        if 'Type' in phishing_df.columns:
            phishing_df = phishing_df.rename(columns={'Type': 'type'})
        phishing_df['label'] = 1  # 1 for phishing
        
        # Load safe URLs
        safe_df = pd.read_csv(safe_file)
        safe_df['label'] = 0  # 0 for safe
        
        # Combine datasets
        combined_df = pd.concat([phishing_df, safe_df], ignore_index=True)
        
        # Clean data
        combined_df = combined_df.dropna()
        combined_df = combined_df[combined_df['url'].str.len() > 0]
        
        print(f"Total samples: {len(combined_df)}")
        print(f"Phishing samples: {len(phishing_df)}")
        print(f"Safe samples: {len(safe_df)}")
        
        return combined_df
    
    def prepare_features(self, df):
        """Extract features from all URLs"""
        print("Extracting features...")
        
        # Extract features for each URL
        feature_arrays = []
        for url in df['url']:
            features = self.extract_features(url)
            feature_arrays.append(features)
        
        # Convert to numpy array
        X = np.array(feature_arrays)
        y = df['label'].values
        
        # Feature names for reference
        self.feature_names = [
            'URL_Length', 'Domain_Length', 'Path_Length', 'Query_Length',
            'Dots_in_Domain', 'Contains_IP', 'Contains_At', 'Uses_HTTPS',
            'Has_Multiple_Subdomains', 'Contains_Hex', 'Contains_Numbers',
            'Contains_Special_Chars', 'URL_Depth', 'Contains_Random_String',
            'Suspicious_TLD', 'Contains_Brand_Name', 'Brand_Name_Count',
            'Suspicious_Word_Count', 'Entropy_Score', 'Has_Redirect',
            'Has_Shortener', 'Has_Typosquatting', 'Has_Homograph'
        ]
        
        print(f"Feature matrix shape: {X.shape}")
        return X, y
    
    def train_model(self, X, y):
        """Train the machine learning model"""
        print("Training model...")
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42, stratify=y
        )
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Feature selection
        self.feature_selector = SelectKBest(score_func=f_classif, k=20)
        X_train_selected = self.feature_selector.fit_transform(X_train_scaled, y_train)
        X_test_selected = self.feature_selector.transform(X_test_scaled)
        
        # Train multiple models for ensemble
        models = {
            'RandomForest': RandomForestClassifier(
                n_estimators=200,
                max_depth=15,
                min_samples_split=5,
                min_samples_leaf=2,
                random_state=42,
                n_jobs=-1
            ),
            'GradientBoosting': GradientBoostingClassifier(
                n_estimators=200,
                max_depth=8,
                learning_rate=0.1,
                random_state=42
            )
        }
        
        best_model = None
        best_score = 0
        
        for name, model in models.items():
            print(f"Training {name}...")
            
            # Cross-validation
            cv_scores = cross_val_score(model, X_train_selected, y_train, cv=5, scoring='accuracy')
            print(f"{name} CV Accuracy: {cv_scores.mean():.4f} (+/- {cv_scores.std() * 2:.4f})")
            
            # Train on full training set
            model.fit(X_train_selected, y_train)
            
            # Evaluate
            y_pred = model.predict(X_test_selected)
            accuracy = accuracy_score(y_test, y_pred)
            
            if accuracy > best_score:
                best_score = accuracy
                best_model = model
                best_model_name = name
        
        self.model = best_model
        print(f"\nBest model: {best_model_name} with accuracy: {best_score:.4f}")
        
        # Final evaluation
        self._evaluate_model(X_test_selected, y_test)
        
        return X_test_selected, y_test
    
    def _evaluate_model(self, X_test, y_test):
        """Evaluate model performance"""
        y_pred = self.model.predict(X_test)
        y_pred_proba = self.model.predict_proba(X_test)
        
        print("\n" + "="*50)
        print("MODEL EVALUATION")
        print("="*50)
        
        # Basic metrics
        accuracy = accuracy_score(y_test, y_pred)
        precision = precision_score(y_test, y_pred)
        recall = recall_score(y_test, y_pred)
        f1 = f1_score(y_test, y_pred)
        
        print(f"Accuracy:  {accuracy:.4f}")
        print(f"Precision: {precision:.4f}")
        print(f"Recall:    {recall:.4f}")
        print(f"F1-Score:  {f1:.4f}")
        
        # Confusion matrix
        cm = confusion_matrix(y_test, y_pred)
        print(f"\nConfusion Matrix:")
        print(cm)
        
        # Classification report
        print(f"\nClassification Report:")
        print(classification_report(y_test, y_pred, target_names=['Safe', 'Phishing']))
        
        # Feature importance
        if hasattr(self.model, 'feature_importances_'):
            self._plot_feature_importance()
        
        # Save evaluation results
        self.model_info = {
            'accuracy': accuracy,
            'precision': precision,
            'recall': recall,
            'f1_score': f1,
            'confusion_matrix': cm.tolist(),
            'training_date': datetime.now().isoformat(),
            'model_type': type(self.model).__name__,
            'feature_count': X_test.shape[1]
        }
    
    def _plot_feature_importance(self):
        """Plot feature importance"""
        if hasattr(self.model, 'feature_importances_'):
            # Get selected feature names
            selected_features = [self.feature_names[i] for i in self.feature_selector.get_support(indices=True)]
            importances = self.model.feature_importances_
            
            # Create feature importance dataframe
            feature_importance = pd.DataFrame({
                'feature': selected_features,
                'importance': importances
            }).sort_values('importance', ascending=False)
            
            # Plot
            plt.figure(figsize=(12, 8))
            sns.barplot(data=feature_importance.head(15), x='importance', y='feature')
            plt.title('Top 15 Most Important Features')
            plt.xlabel('Feature Importance')
            plt.tight_layout()
            plt.savefig('feature_importance.png', dpi=300, bbox_inches='tight')
            plt.close()
            
            print(f"\nTop 10 Most Important Features:")
            for i, row in feature_importance.head(10).iterrows():
                print(f"{row['feature']}: {row['importance']:.4f}")
    
    def save_model(self, filename='phishing_model.joblib'):
        """Save the trained model and related components"""
        print(f"\nSaving model to {filename}...")
        
        model_data = {
            'model': self.model,
            'scaler': self.scaler,
            'feature_selector': self.feature_selector,
            'feature_names': self.feature_names,
            'model_info': self.model_info
        }
        
        joblib.dump(model_data, filename)
        print("Model saved successfully!")
        
        # Save model info as JSON
        with open('model_info.json', 'w') as f:
            json.dump(self.model_info, f, indent=2)
        
        print("Model information saved to model_info.json")
    
    def predict_single(self, url):
        """Make prediction for a single URL"""
        if self.model is None:
            raise ValueError("Model not trained yet!")
        
        # Extract features
        features = self.extract_features(url)
        features = np.array(features).reshape(1, -1)
        
        # Scale features
        features_scaled = self.scaler.transform(features)
        
        # Select features
        features_selected = self.feature_selector.transform(features_scaled)
        
        # Make prediction
        prediction = self.model.predict(features_selected)[0]
        confidence = self.model.predict_proba(features_selected)[0]
        
        return {
            'url': url,
            'prediction': 'phishing' if prediction == 1 else 'safe',
            'confidence': max(confidence),
            'confidence_scores': {
                'safe': confidence[0],
                'phishing': confidence[1]
            },
            'features': dict(zip(self.feature_names, features[0]))
        }

def main():
    """Main training function"""
    print("="*60)
    print("ADVANCED PHISHING DETECTION MODEL TRAINER")
    print("="*60)
    
    # Initialize trainer
    trainer = PhishingDetectorTrainer()
    
    # Load data
    phishing_file = 'phishing.csv'
    safe_file = 'safe.csv'
    
    if not os.path.exists(phishing_file) or not os.path.exists(safe_file):
        print("Error: Training data files not found!")
        print("Please ensure phishing.csv and safe.csv are in the current directory.")
        return
    
    # Load and prepare data
    df = trainer.load_data(phishing_file, safe_file)
    X, y = trainer.prepare_features(df)
    
    # Train model
    X_test, y_test = trainer.train_model(X, y)
    
    # Save model
    trainer.save_model()
    
    # Test prediction
    test_url = "https://www.google.com"
    try:
        result = trainer.predict_single(test_url)
        print(f"\nTest prediction for {test_url}:")
        print(f"Result: {result['prediction']}")
        print(f"Confidence: {result['confidence']:.4f}")
    except Exception as e:
        print(f"Error testing prediction: {e}")
    
    print("\n" + "="*60)
    print("TRAINING COMPLETED SUCCESSFULLY!")
    print("="*60)

if __name__ == "__main__":
    main()
