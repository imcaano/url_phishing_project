#!/usr/bin/env python3
"""
Complete Training and Deployment Script
Trains the ML model and starts the Flask API
"""

import os
import sys
import subprocess
import time
import requests
import json
from pathlib import Path

def check_dependencies():
    """Check if required packages are installed"""
    print("Checking dependencies...")
    
    required_packages = [
        'pandas', 'numpy', 'scikit-learn', 'joblib', 
        'matplotlib', 'seaborn', 'flask', 'flask-cors'
    ]
    
    missing_packages = []
    
    for package in required_packages:
        try:
            __import__(package.replace('-', '_'))
            print(f"✓ {package}")
        except ImportError:
            missing_packages.append(package)
            print(f"✗ {package} - MISSING")
    
    if missing_packages:
        print(f"\nMissing packages: {', '.join(missing_packages)}")
        print("Installing missing packages...")
        
        try:
            subprocess.check_call([
                sys.executable, '-m', 'pip', 'install'
            ] + missing_packages)
            print("Dependencies installed successfully!")
        except subprocess.CalledProcessError:
            print("Failed to install dependencies. Please install manually:")
            print(f"pip install {' '.join(missing_packages)}")
            return False
    
    return True

def train_model():
    """Train the machine learning model"""
    print("\n" + "="*60)
    print("TRAINING MACHINE LEARNING MODEL")
    print("="*60)
    
    # Check if training data exists
    if not os.path.exists('phishing.csv') or not os.path.exists('safe.csv'):
        print("Error: Training data files not found!")
        print("Please ensure phishing.csv and safe.csv are in the current directory.")
        return False
    
    try:
        # Import and run training
        from train_model import main as train_main
        train_main()
        return True
    except Exception as e:
        print(f"Error during training: {e}")
        return False

def start_api():
    """Start the Flask API"""
    print("\n" + "="*60)
    print("STARTING FLASK API")
    print("="*60)
    
    # Check if model exists
    if not os.path.exists('phishing_model.joblib'):
        print("Error: Trained model not found!")
        print("Please train the model first.")
        return False
    
    try:
        # Start the API in a subprocess
        print("Starting Flask API on http://localhost:5000...")
        
        # Use subprocess.Popen to run the API in background
        api_process = subprocess.Popen([
            sys.executable, 'predict_api.py'
        ], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        
        # Wait a bit for the API to start
        print("Waiting for API to start...")
        time.sleep(5)
        
        # Test the API
        try:
            response = requests.get('http://localhost:5000/health', timeout=10)
            if response.status_code == 200:
                data = response.json()
                if data.get('status') == 'healthy':
                    print("✓ API started successfully!")
                    print(f"Model loaded: {data.get('model_loaded', 'Unknown')}")
                    return api_process
                else:
                    print("✗ API health check failed")
                    return False
            else:
                print(f"✗ API health check failed with status code: {response.status_code}")
                return False
        except requests.exceptions.RequestException as e:
            print(f"✗ API health check failed: {e}")
            return False
            
    except Exception as e:
        print(f"Error starting API: {e}")
        return False

def test_api():
    """Test the API with sample URLs"""
    print("\n" + "="*60)
    print("TESTING API")
    print("="*60)
    
    test_urls = [
        "https://www.google.com",
        "https://www.youtube.com",
        "https://www.facebook.com",
        "https://example-suspicious-site.tk",
        "https://fake-login-site.xyz"
    ]
    
    for url in test_urls:
        try:
            print(f"\nTesting: {url}")
            response = requests.post(
                'http://localhost:5000/predict',
                json={'url': url},
                timeout=10
            )
            
            if response.status_code == 200:
                data = response.json()
                if data.get('status') == 'success':
                    prediction = data.get('prediction', 'unknown')
                    confidence = data.get('confidence_score', 0)
                    risk_level = data.get('risk_level', 'unknown')
                    
                    print(f"  Result: {prediction.upper()}")
                    print(f"  Confidence: {confidence}%")
                    print(f"  Risk Level: {risk_level}")
                else:
                    print(f"  Error: {data.get('error', 'Unknown error')}")
            else:
                print(f"  HTTP Error: {response.status_code}")
                
        except requests.exceptions.RequestException as e:
            print(f"  Request Error: {e}")

def create_startup_scripts():
    """Create startup scripts for different platforms"""
    print("\n" + "="*60)
    print("CREATING STARTUP SCRIPTS")
    print("="*60)
    
    # Windows batch file
    windows_script = """@echo off
echo Starting Phishing Detection API...
cd /d "%~dp0"
python predict_api.py
pause
"""
    
    with open('start_api.bat', 'w') as f:
        f.write(windows_script)
    
    # Linux/Mac shell script
    unix_script = """#!/bin/bash
echo "Starting Phishing Detection API..."
cd "$(dirname "$0")"
python3 predict_api.py
"""
    
    with open('start_api.sh', 'w') as f:
        f.write(unix_script)
    
    # Make shell script executable on Unix systems
    try:
        os.chmod('start_api.sh', 0o755)
    except:
        pass
    
    print("✓ Created startup scripts:")
    print("  - start_api.bat (Windows)")
    print("  - start_api.sh (Linux/Mac)")

def create_config_file():
    """Create configuration file for the API"""
    print("\n" + "="*60)
    print("CREATING CONFIGURATION")
    print("="*60)
    
    config = {
        "api": {
            "host": "0.0.0.0",
            "port": 5000,
            "debug": False,
            "threaded": True
        },
        "model": {
            "path": "phishing_model.joblib",
            "timeout": 30,
            "max_retries": 3
        },
        "logging": {
            "level": "INFO",
            "file": "api.log"
        }
    }
    
    with open('config.json', 'w') as f:
        json.dump(config, f, indent=2)
    
    print("✓ Created config.json")

def main():
    """Main execution function"""
    print("="*60)
    print("PHISHING DETECTION - TRAINING & DEPLOYMENT")
    print("="*60)
    
    # Change to script directory
    script_dir = Path(__file__).parent
    os.chdir(script_dir)
    
    # Check dependencies
    if not check_dependencies():
        print("Failed to install dependencies. Exiting.")
        return
    
    # Train model
    if not train_model():
        print("Model training failed. Exiting.")
        return
    
    # Create configuration
    create_config_file()
    
    # Create startup scripts
    create_startup_scripts()
    
    # Start API
    api_process = start_api()
    if not api_process:
        print("Failed to start API. Exiting.")
        return
    
    # Test API
    test_api()
    
    print("\n" + "="*60)
    print("DEPLOYMENT COMPLETED SUCCESSFULLY!")
    print("="*60)
    print("\nYour phishing detection API is now running!")
    print("API URL: http://localhost:5000")
    print("\nAvailable endpoints:")
    print("  - GET  /health      - Health check")
    print("  - POST /predict     - Single URL prediction")
    print("  - POST /batch_predict - Batch URL prediction")
    print("  - GET  /model_info  - Model information")
    print("\nTo stop the API, press Ctrl+C")
    print("\nTo restart later, use:")
    print("  - Windows: start_api.bat")
    print("  - Linux/Mac: ./start_api.sh")
    
    try:
        # Keep the script running
        api_process.wait()
    except KeyboardInterrupt:
        print("\nShutting down API...")
        api_process.terminate()
        api_process.wait()
        print("API stopped.")

if __name__ == "__main__":
    main()
