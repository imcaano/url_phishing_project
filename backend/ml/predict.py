from flask import Flask, request, jsonify
from flask_cors import CORS
from pycaret.classification import load_model, predict_model
from featureExtractor import featureExtraction
from urllib.parse import urlparse

app = Flask(__name__)
CORS(app)

model = load_model('model/phishingdetection')

FREE_HOSTS = [
    'weebly.com', '000webhostapp.com', 'wixsite.com', 'blogspot.com', 'wordpress.com'
]
SUSPICIOUS_SUBS = [
    'sign', 'login', 'webmail', 'secure', 'update', 'verify'
]

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        url = data.get('url')
        if not url:
            return jsonify({'error': 'URL is required'}), 400
        # Expert heuristic: check for suspicious subdomain on free host
        parsed = urlparse(url)
        domain = parsed.netloc.lower()
        for host in FREE_HOSTS:
            if domain.endswith(host):
                subdomain = domain.replace('.' + host, '')
                for keyword in SUSPICIOUS_SUBS:
                    if keyword in subdomain:
                        return jsonify({
                            'is_phishing': True,
                            'confidence_score': 99.0,
                            'features': {},
                            'expert_explanation': f'Flagged by expert heuristic: suspicious subdomain ("{keyword}") on free host ("{host}")'
                        })
        features_df = featureExtraction(url)
        result = predict_model(model, data=features_df)
        prediction_score = result['prediction_score'][0]
        prediction_label = result['prediction_label'][0]
        return jsonify({
            'is_phishing': bool(prediction_label),
            'confidence_score': float(prediction_score * 100),
            'features': features_df.iloc[0].to_dict()
        })
    except Exception as e:
        print(f"Error during prediction: {str(e)}")
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000, debug=True) 