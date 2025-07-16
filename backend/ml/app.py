from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import re

app = Flask(__name__)
CORS(app)

# Load the trained model
model = joblib.load('phishing_model.joblib')

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

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        url = data.get('url')
        
        if not url:
            return jsonify({'error': 'URL is required'}), 400
            
        # Extract features
        features = extract_features(url)
        feature_values = list(features.values())
        
        # Make prediction
        prediction = model.predict([feature_values])[0]
        probability = model.predict_proba([feature_values])[0][1]
        
        return jsonify({
            'is_phishing': bool(prediction),
            'confidence_score': float(probability * 100),
            'features': features
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000) 