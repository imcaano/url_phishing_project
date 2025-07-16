# URL Phishing Detection System

## Setup Instructions

1. Database Setup:
   ```bash
   # Create database and tables
   php backend/setup.php
   ```

2. Install Dependencies:
   ```bash
   composer install
   ```

3. Configure Environment:
   - Copy `.env.example` to `.env`
   - Update database credentials in `.env`

4. Start Development Server:
   ```bash
   php -S localhost:8000 -t public/
   ```

5. Access the Application:
   - Open http://localhost:8000 in your browser
   - Login with default admin credentials:
     - Username: admin
     - Password: admin123

## Project Structure 