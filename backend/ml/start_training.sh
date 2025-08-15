#!/bin/bash

echo "========================================"
echo "PHISHING DETECTION ML TRAINING"
echo "========================================"
echo ""
echo "This script will train the machine learning model"
echo "and start the prediction API."
echo ""
echo "Make sure you have Python 3 installed and the"
echo "training datasets (phishing.csv and safe.csv) are"
echo "in the current directory."
echo ""
read -p "Press Enter to continue..."

echo ""
echo "Starting training process..."
echo ""

python3 train_and_deploy.py

echo ""
echo "Training completed!"
echo ""
read -p "Press Enter to exit..."
