#!/usr/bin/env python3
"""
Debug version of the Flask API to identify errors
"""

from flask import Flask, request, jsonify
import joblib
import numpy as np
import re
from urllib.parse import urlparse
import logging
import traceback

# Configure logging
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)

app = Flask(__name__)

class SimplePredictor:
    def __init__(self):
        try:
            logger.info("Loading model...")
            self.model_data = joblib.load('phishing_model.joblib')
            self.model = self.model_data['model']
            self.scaler = self.model_data['scaler']
            self.feature_selector = self.model_data['feature_selector']
            logger.info("Model loaded successfully")
            logger.info(f"Model type: {type(self.model)}")
            logger.info(f"Scaler type: {type(self.scaler)}")
            logger.info(f"Feature selector type: {type(self.feature_selector)}")
        except Exception as e:
            logger.error(f"Error loading model: {e}")
            logger.error(traceback.format_exc())
            self.model = None
    
    def extract_features(self, url):
        """Simple feature extraction"""
        try:
            parsed = urlparse(url)
            
            # Basic features only
            url_length = len(url)
            domain_length = len(parsed.netloc)
            path_length = len(parsed.path)
            query_length = len(parsed.query)
            dots_in_domain = parsed.netloc.count('.')
            contains_ip = bool(re.match(r'^(\d{1,3}\.){3}\d{1,3}$', parsed.netloc))
            contains_at = '@' in url
            uses_https = parsed.scheme == 'https'
            
            features = [
                url_length, domain_length, path_length, query_length,
                dots_in_domain, int(contains_ip), int(contains_at), int(uses_https),
                0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0
            ]
            
            logger.info(f"Extracted features: {features}")
            return features
            
        except Exception as e:
            logger.error(f"Error extracting features: {e}")
            logger.error(traceback.format_exc())
            return [0] * 23
    
    def predict(self, url):
        """Make prediction for a URL"""
        try:
            if self.model is None:
                return {'error': 'Model not loaded', 'status': 'error'}
            
            logger.info(f"Making prediction for URL: {url}")
            
            # Extract features
            features = self.extract_features(url)
            features = np.array(features).reshape(1, -1)
            logger.info(f"Features shape: {features.shape}")
            
            # Scale features
            features_scaled = self.scaler.transform(features)
            logger.info(f"Scaled features shape: {features_scaled.shape}")
            
            # Select features
            features_selected = self.feature_selector.transform(features_scaled)
            logger.info(f"Selected features shape: {features_selected.shape}")
            
            # Make prediction
            prediction = self.model.predict(features_selected)[0]
            confidence_proba = self.model.predict_proba(features_selected)[0]
            
            logger.info(f"Prediction: {prediction}")
            logger.info(f"Confidence proba: {confidence_proba}")
            
            confidence = max(confidence_proba)
            
            result = {
                'url': url,
                'prediction': 'phishing' if prediction == 1 else 'safe',
                'confidence_score': round(confidence * 100, 2),
                'risk_level': 'HIGH' if confidence > 0.8 else 'MEDIUM' if confidence > 0.6 else 'LOW',
                'status': 'success'
            }
            
            logger.info(f"Result: {result}")
            return result
            
        except Exception as e:
            logger.error(f"Error making prediction: {e}")
            logger.error(traceback.format_exc())
            return {'error': str(e), 'status': 'error'}

# Initialize predictor
predictor = SimplePredictor()

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({
        'status': 'healthy',
        'model_loaded': predictor.model is not None,
        'timestamp': '2025-08-14'
    })

@app.route('/predict', methods=['GET', 'POST'])
def predict_url():
    try:
        # Handle both GET and POST requests
        if request.method == 'GET':
            url = request.args.get('url', '').strip()
        else:
            data = request.get_json()
            url = data.get('url', '').strip() if data else ''
        
        if not url:
            return jsonify({'error': 'URL parameter is required', 'status': 'error'}), 400
        
        logger.info(f"Received request for URL: {url}")
        
        # Make prediction
        result = predictor.predict(url)
        
        if result['status'] == 'error':
            return jsonify(result), 500
        
        return jsonify(result)
        
    except Exception as e:
        logger.error(f"Error in prediction endpoint: {e}")
        logger.error(traceback.format_exc())
        return jsonify({'error': 'Internal server error', 'status': 'error'}), 500

if __name__ == '__main__':
    logger.info("Starting Debug API...")
    app.run(host='0.0.0.0', port=5000, debug=True)
