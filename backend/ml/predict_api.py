#!/usr/bin/env python3
"""
Flask API for Phishing Detection Predictions
Provides real-time URL analysis using the trained machine learning model
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import numpy as np
import re
from urllib.parse import urlparse
import json
import logging
from datetime import datetime
import os

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

class PhishingPredictor:
    def __init__(self, model_path='phishing_model.joblib'):
        """Initialize the predictor with trained model"""
        try:
            if os.path.exists(model_path):
                self.model_data = joblib.load(model_path)
                self.model = self.model_data['model']
                self.scaler = self.model_data['scaler']
                self.feature_selector = self.model_data['feature_selector']
                self.feature_names = self.model_data.get('feature_names', [])
                self.model_info = self.model_data.get('model_info', {})
                
                # Fallback feature names if not loaded
                if not self.feature_names:
                    self.feature_names = [
                        'URL_Length', 'Domain_Length', 'Path_Length', 'Query_Length',
                        'Dots_in_Domain', 'Contains_IP', 'Contains_At', 'Uses_HTTPS',
                        'Has_Multiple_Subdomains', 'Contains_Hex', 'Contains_Numbers',
                        'Contains_Special_Chars', 'URL_Depth', 'Contains_Random_String',
                        'Suspicious_TLD', 'Contains_Brand_Name', 'Brand_Name_Count',
                        'Suspicious_Word_Count', 'Entropy_Score', 'Has_Redirect',
                        'Has_Shortener', 'Has_Typosquatting', 'Has_Homograph'
                    ]
                
                logger.info("Model loaded successfully")
            else:
                logger.error(f"Model file not found: {model_path}")
                self.model = None
        except Exception as e:
            logger.error(f"Error loading model: {e}")
            self.model = None
    
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
            
            # Brand name detection
            legitimate_brands = ['google', 'facebook', 'youtube', 'amazon', 'microsoft', 'apple', 'netflix', 'twitter', 'instagram', 'linkedin']
            contains_brand_name = any(brand in parsed.netloc.lower() for brand in legitimate_brands)
            brand_name_count = sum(1 for brand in legitimate_brands if brand in parsed.netloc.lower())
            
            # Suspicious words detection
            suspicious_words = ['login', 'signin', 'verify', 'secure', 'account', 'banking', 'paypal', 'ebay', 'amazon', 'update', 'confirm', 'verify', 'security']
            suspicious_word_count = sum(1 for word in suspicious_words if word in url.lower())
            
            # Entropy calculation
            def calculate_entropy(text):
                if not text:
                    return 0
                prob = [float(text.count(c)) / len(text) for c in set(text)]
                entropy = -sum(p * np.log2(p) for p in prob)
                return entropy
            
            entropy_score = calculate_entropy(parsed.netloc)
            
            # Additional features
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
            logger.error(f"Error extracting features: {e}")
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
        """Detect potential homograph attacks"""
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
    
    def predict(self, url):
        """Make prediction for a URL"""
        if self.model is None:
            return {
                'error': 'Model not loaded',
                'status': 'error'
            }
        
        try:
            # Extract features
            features = self.extract_features(url)
            features = np.array(features).reshape(1, -1)
            
            # Scale features
            features_scaled = self.scaler.transform(features)
            
            # Select features
            features_selected = self.feature_selector.transform(features_scaled)
            
            # Make prediction
            prediction = self.model.predict(features_selected)[0]
            confidence_proba = self.model.predict_proba(features_selected)[0]
            
            # Calculate confidence score
            confidence = max(confidence_proba)
            
            # Determine risk level
            if confidence >= 0.9:
                risk_level = "VERY_HIGH"
            elif confidence >= 0.8:
                risk_level = "HIGH"
            elif confidence >= 0.7:
                risk_level = "MEDIUM"
            elif confidence >= 0.6:
                risk_level = "LOW"
            else:
                risk_level = "VERY_LOW"
            
            # Create detailed analysis
            analysis = self._create_expert_analysis(url, features, prediction, confidence, risk_level)
            
            result = {
                'url': url,
                'prediction': 'phishing' if prediction == 1 else 'safe',
                'confidence_score': round(confidence * 100, 2),
                'risk_level': risk_level,
                'confidence_scores': {
                    'safe': round(confidence_proba[0] * 100, 2),
                    'phishing': round(confidence_proba[1] * 100, 2)
                },
                'features': dict(zip(self.feature_names, [float(x) for x in features.flatten()])),
                'expert_analysis': analysis,
                'model_info': {
                    'model_type': self.model_info.get('model_type', 'Unknown'),
                    'accuracy': self.model_info.get('accuracy', 'Unknown'),
                    'training_date': self.model_info.get('training_date', 'Unknown')
                },
                'timestamp': datetime.now().isoformat(),
                'status': 'success'
            }
            
            return result
            
        except Exception as e:
            logger.error(f"Error making prediction: {e}")
            return {
                'error': str(e),
                'status': 'error'
            }
    
    def _create_expert_analysis(self, url, features, prediction, confidence, risk_level):
        """Create detailed expert analysis"""
        parsed = urlparse(url)
        domain = parsed.netloc
        
        # Convert features to list if it's a numpy array
        if hasattr(features, 'flatten'):
            features = features.flatten().tolist()
        
        analysis = {
            'summary': '',
            'risk_factors': [],
            'security_features': [],
            'recommendations': [],
            'technical_details': {}
        }
        
        # Determine summary
        if prediction == 1:  # Phishing
            analysis['summary'] = f"🚨 **PHISHING DETECTED**: This URL has been identified as a potential phishing threat with {confidence*100:.1f}% confidence."
        else:  # Safe
            analysis['summary'] = f"✅ **SAFE**: This URL appears to be legitimate and safe to visit with {confidence*100:.1f}% confidence."
        
        # Analyze risk factors - use safe indexing
        if len(features) > 4 and features[4] > 3:  # Many dots in domain
            analysis['risk_factors'].append("Multiple subdomains detected")
        
        if len(features) > 5 and features[5]:  # Contains IP
            analysis['risk_factors'].append("Uses IP address instead of domain name")
        
        if len(features) > 6 and features[6]:  # Contains @
            analysis['risk_factors'].append("Contains @ symbol (suspicious)")
        
        if len(features) > 7 and not features[7]:  # No HTTPS
            analysis['risk_factors'].append("No HTTPS encryption")
        
        if len(features) > 13 and features[13]:  # Random string
            analysis['risk_factors'].append("Contains random strings")
        
        if len(features) > 14 and features[14]:  # Suspicious TLD
            analysis['risk_factors'].append("Uses suspicious top-level domain")
        
        if len(features) > 17 and features[17] > 0:  # Suspicious words
            analysis['risk_factors'].append(f"Contains {features[17]} suspicious keywords")
        
        # Security features
        if len(features) > 7 and features[7]:  # HTTPS
            analysis['security_features'].append("HTTPS encryption enabled")
        
        if len(features) > 15 and features[15]:  # Brand name
            analysis['security_features'].append("Contains legitimate brand name")
        
        # Recommendations
        if prediction == 1:
            analysis['recommendations'] = [
                "Do not visit this URL under any circumstances",
                "Do not enter any personal information",
                "Report this URL to your IT security team",
                "Consider adding this domain to your blacklist",
                "If you already visited this URL, change your passwords immediately"
            ]
        else:
            analysis['recommendations'] = [
                "This URL appears safe to visit",
                "Always verify you're on the correct domain",
                "Check for HTTPS encryption",
                "Be cautious with personal information"
            ]
        
        # Technical details
        analysis['technical_details'] = {
            'domain': domain,
            'url_length': float(features[0]) if len(features) > 0 else 0,
            'domain_length': float(features[1]) if len(features) > 1 else 0,
            'entropy_score': round(float(features[18]), 3) if len(features) > 18 else 0,
            'suspicious_features_count': sum([
                float(features[4]) > 3 if len(features) > 4 else False,
                bool(features[5]) if len(features) > 5 else False,
                bool(features[6]) if len(features) > 6 else False,
                not bool(features[7]) if len(features) > 7 else False,
                bool(features[13]) if len(features) > 13 else False,
                bool(features[14]) if len(features) > 14 else False,
                float(features[17]) > 0 if len(features) > 17 else False
            ])
        }
        
        return analysis

# Initialize predictor
predictor = PhishingPredictor()

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'model_loaded': predictor.model is not None,
        'timestamp': datetime.now().isoformat()
    })

@app.route('/predict', methods=['GET', 'POST'])
def predict_url():
    """Main prediction endpoint"""
    try:
        # Handle both GET and POST requests
        if request.method == 'GET':
            url = request.args.get('url', '').strip()
        else:
            data = request.get_json()
            url = data.get('url', '').strip() if data else ''
        
        if not url:
            return jsonify({
                'error': 'URL parameter is required',
                'status': 'error'
            }), 400
        
        if not url:
            return jsonify({
                'error': 'URL cannot be empty',
                'status': 'error'
            }), 400
        
        # Validate URL format
        try:
            parsed = urlparse(url)
            if not parsed.scheme or not parsed.netloc:
                return jsonify({
                    'error': 'Invalid URL format',
                    'status': 'error'
                }), 400
        except Exception:
            return jsonify({
                'error': 'Invalid URL format',
                'status': 'error'
            }), 400
        
        # Make prediction
        result = predictor.predict(url)
        
        if result['status'] == 'error':
            return jsonify(result), 500
        
        return jsonify(result)
        
    except Exception as e:
        logger.error(f"Error in prediction endpoint: {e}")
        return jsonify({
            'error': 'Internal server error',
            'status': 'error'
        }), 500

@app.route('/batch_predict', methods=['POST'])
def batch_predict():
    """Batch prediction endpoint for multiple URLs"""
    try:
        data = request.get_json()
        
        if not data or 'urls' not in data:
            return jsonify({
                'error': 'URLs array is required',
                'status': 'error'
            }), 400
        
        urls = data['urls']
        
        if not isinstance(urls, list) or len(urls) == 0:
            return jsonify({
                'error': 'URLs must be a non-empty array',
                'status': 'error'
            }), 400
        
        if len(urls) > 100:  # Limit batch size
            return jsonify({
                'error': 'Maximum 100 URLs allowed per batch',
                'status': 'error'
            }), 400
        
        results = []
        for url in urls:
            try:
                result = predictor.predict(url.strip())
                results.append(result)
            except Exception as e:
                results.append({
                    'url': url,
                    'error': str(e),
                    'status': 'error'
                })
        
        return jsonify({
            'results': results,
            'total_urls': len(urls),
            'successful_predictions': len([r for r in results if r['status'] == 'success']),
            'timestamp': datetime.now().isoformat(),
            'status': 'success'
        })
        
    except Exception as e:
        logger.error(f"Error in batch prediction endpoint: {e}")
        return jsonify({
            'error': 'Internal server error',
            'status': 'error'
        }), 500

@app.route('/model_info', methods=['GET'])
def get_model_info():
    """Get model information"""
    if predictor.model is None:
        return jsonify({
            'error': 'Model not loaded',
            'status': 'error'
        }), 500
    
    return jsonify({
        'model_info': predictor.model_info,
        'feature_names': predictor.feature_names,
        'status': 'success'
    })

@app.errorhandler(404)
def not_found(error):
    return jsonify({
        'error': 'Endpoint not found',
        'status': 'error'
    }), 404

@app.errorhandler(500)
def internal_error(error):
    return jsonify({
        'error': 'Internal server error',
        'status': 'error'
    }), 500

if __name__ == '__main__':
    # Check if model is loaded
    if predictor.model is None:
        logger.error("Failed to load model. Please ensure the model file exists.")
        exit(1)
    
    logger.info("Starting Phishing Detection API...")
    logger.info(f"Model loaded: {predictor.model_info.get('model_type', 'Unknown')}")
    logger.info(f"Model accuracy: {predictor.model_info.get('accuracy', 'Unknown')}")
    
    # Run the Flask app
    app.run(
        host='0.0.0.0',
        port=5000,
        debug=False,
        threaded=True
    )
