#!/bin/bash
echo "Starting Phishing Detection API..."
cd "$(dirname "$0")"
python3 predict_api.py
