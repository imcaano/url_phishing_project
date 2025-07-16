@echo off
echo Starting URL Phishing Detection Services...

:: Start Python ML Service
cd backend\ml
start cmd /k "venv\Scripts\activate && python app.py"

:: Start PHP Service (XAMPP)
echo Starting XAMPP...
start "" "C:\xampp\xampp-control.exe"

echo Services started successfully!
echo ML Service running on http://127.0.0.1:5000
echo PHP Service running on http://localhost/url_phishing_project 