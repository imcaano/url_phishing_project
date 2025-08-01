# URL Phishing Detection ML Service

This is the machine learning service for the URL Phishing Detection system. It provides a REST API endpoint that uses a trained Random Forest model to predict whether a URL is potentially phishing or not.

## Setup

1. Create a Python virtual environment:
```bash
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
```

2. Install dependencies:
```bash
pip install -r requirements.txt
```

3. Make sure the trained model file `phishing_model.joblib` is in the same directory as `app.py`

## Running the Service

Start the Flask server:
```bash
python app.py
```

The service will run on `http://127.0.0.1:5000`

## API Endpoint

### POST /predict

Predicts whether a URL is potentially phishing.

Request body:
```json
{
    "url": "https://example.com"
}
```

Response:
```json
{
    "is_phishing": false,
    "confidence_score": 85.5,
    "features": {
        "url_length": 20,
        "domain_length": 11,
        "path_length": 0,
        "dots_in_domain": 1,
        "contains_ip": 0,
        "contains_at": 0,
        "uses_https": 1,
        "multiple_subdomains": 0,
        "contains_hex": 0,
        "contains_numbers": 0,
        "contains_special_chars": 0,
        "contains_random_string": 0
    }
}
```

## Features

The model uses the following features to make predictions:
- URL length
- Domain length
- Path length
- Number of dots in domain
- Contains IP address
- Contains @ symbol
- Uses HTTPS
- Has multiple subdomains
- Contains hexadecimal characters
- Contains numbers
- Contains special characters
- Contains random strings 