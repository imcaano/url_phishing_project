<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ML Model Status Check</title>
    <link rel="icon" type="image/png" href="/url_phishing_project/public/assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .check-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }
        
        .status-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .status-success {
            color: #28a745;
        }
        
        .status-error {
            color: #dc3545;
        }
        
        .status-loading {
            color: #ffc107;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        .model-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        
        .model-info h5 {
            color: #495057;
            margin-bottom: 1rem;
        }
        
        .model-info p {
            margin-bottom: 0.5rem;
            color: #6c757d;
        }
        
        .model-info strong {
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="check-container">
        <div id="loading-status">
            <div class="status-icon status-loading">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <h2 class="mb-3">Checking ML Model Status...</h2>
            <p class="text-muted">Please wait while we verify the machine learning model is running</p>
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div id="success-status" style="display: none;">
            <div class="status-icon status-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="mb-3 text-success">ML Model is Running! ✅</h2>
            <p class="text-muted mb-4">Your phishing detection system is ready to use</p>
            
            <div class="model-info">
                <h5><i class="fas fa-robot me-2"></i>Model Information</h5>
                <p><strong>Status:</strong> <span class="text-success">Active</span></p>
                <p><strong>Accuracy:</strong> <span class="text-success">96.87%</span></p>
                <p><strong>Features:</strong> <span class="text-success">23+ URL Analysis Features</span></p>
                <p><strong>API Endpoint:</strong> <span class="text-success">http://localhost:5000</span></p>
            </div>
            
            <a href="/url_phishing_project/public/dashboard" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-right me-2"></i>Continue to Dashboard
            </a>
        </div>

        <div id="error-status" style="display: none;">
            <div class="status-icon status-error">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2 class="mb-3 text-danger">ML Model Not Running! ❌</h2>
            <p class="text-muted mb-4">The machine learning model must be started before using the web interface</p>
            
            <div class="model-info">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Required Steps</h5>
                <p><strong>1.</strong> Open a new terminal/command prompt</p>
                <p><strong>2.</strong> Navigate to: <code>backend/ml</code></p>
                <p><strong>3.</strong> Run: <code>python predict_api.py</code></p>
                <p><strong>4.</strong> Wait for: <code>Running on http://127.0.0.1:5000</code></p>
                <p><strong>5.</strong> Keep the terminal running</p>
            </div>
            
            <div class="alert alert-warning">
                <strong>Note:</strong> The ML model must be running continuously in the background for the web interface to work properly.
            </div>
            
            <button onclick="checkModelStatus()" class="btn btn-primary btn-lg">
                <i class="fas fa-sync-alt me-2"></i>Check Again
            </button>
        </div>
    </div>

    <script>
        // Check model status when page loads
        document.addEventListener('DOMContentLoaded', function() {
            checkModelStatus();
        });

        async function checkModelStatus() {
            const loadingStatus = document.getElementById('loading-status');
            const successStatus = document.getElementById('success-status');
            const errorStatus = document.getElementById('error-status');

            // Show loading
            loadingStatus.style.display = 'block';
            successStatus.style.display = 'none';
            errorStatus.style.display = 'none';

            try {
                const response = await fetch('http://localhost:5000/health', {
                    method: 'GET',
                    timeout: 5000
                });

                if (response.ok) {
                    const data = await response.json();
                    
                    // Hide loading, show success
                    loadingStatus.style.display = 'none';
                    successStatus.style.display = 'block';
                    
                    // Set session flag and redirect
                    fetch('/url_phishing_project/public/set-model-checked', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        }
                    }).then(() => {
                        // Auto-redirect after 3 seconds
                        setTimeout(() => {
                            window.location.href = '/url_phishing_project/public/dashboard';
                        }, 3000);
                    });
                } else {
                    throw new Error('Model not responding properly');
                }
            } catch (error) {
                console.error('ML Model check failed:', error);
                
                // Hide loading, show error
                loadingStatus.style.display = 'none';
                errorStatus.style.display = 'block';
            }
        }
    </script>
</body>
</html>
