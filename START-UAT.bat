@echo off
REM Liwonde Sun Hotel - Development Startup Script
REM Use this script to start the UAT environment for testing

echo.
echo ================================================
echo    Liwonde Sun Hotel - UAT Environment Setup
echo ================================================
echo.

REM Check if PHP is installed
php --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP is not installed or not in your PATH.
    echo Please install PHP and ensure it's accessible from the command line.
    echo.
    pause
    exit /b 1
)

REM Start the PHP built-in server
echo Starting UAT server on http://localhost:8000
echo.
echo NOTE: The site will automatically detect UAT mode when running locally
echo       and switch to PROD mode when deployed to liwondesunhotel.com
echo.
echo Press Ctrl+C to stop the server
echo ================================================

php -S localhost:8000

echo.
echo Server stopped.
echo ================================================
pause