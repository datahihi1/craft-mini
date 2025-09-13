@echo off
REM CraftMini Framework Test Runner for Windows
REM This script runs comprehensive tests on the framework

echo 🧪 CraftMini Framework Test Runner
echo ==================================
echo.

REM Check if PHP is available
echo 1. Checking PHP installation...
php -v >nul 2>&1
if %errorlevel% equ 0 (
    echo ✅ PHP is installed
    php -v | findstr "PHP"
) else (
    echo ❌ PHP is not installed
    exit /b 1
)

REM Check if Composer is available
echo.
echo 2. Checking Composer installation...
composer --version >nul 2>&1
if %errorlevel% equ 0 (
    echo ✅ Composer is installed
) else (
    echo ❌ Composer is not installed
    exit /b 1
)

REM Install dependencies
echo.
echo 3. Installing dependencies...
composer install --no-progress --prefer-dist --optimize-autoloader
if %errorlevel% equ 0 (
    echo ✅ Dependencies installed successfully
) else (
    echo ❌ Failed to install dependencies
    exit /b 1
)

REM Run basic framework test
echo.
echo 4. Running framework tests...
php test-framework.php
if %errorlevel% equ 0 (
    echo ✅ Framework tests passed
) else (
    echo ❌ Framework tests failed
    exit /b 1
)

REM Create test environment
echo.
echo 5. Setting up test environment...
if not exist "public\logs" mkdir public\logs
echo. > public\test_manga_readers.db
echo APP_ENVIRONMENT=testing > .env
echo APP_DEBUG=true >> .env
echo ✅ Test environment created

REM Start web server and test routes
echo.
echo 6. Testing web routes...
echo Starting PHP built-in server...

REM Start server in background
start /b php -S localhost:8000 -t public/ > server.log 2>&1

REM Wait for server to start
timeout /t 3 /nobreak >nul

REM Test default route
echo Testing default route (/)...
curl -f -s http://localhost:8000/ > response.html 2>nul
if %errorlevel% equ 0 (
    findstr /C:"Xin chào" response.html >nul
    if %errorlevel% equ 0 (
        echo ✅ Default route returns expected content
    ) else (
        echo ❌ Default route content mismatch
        echo Response content:
        type response.html
    )
) else (
    echo ❌ Default route not accessible
    echo Server log:
    type server.log
)

REM Test API routes
echo.
echo Testing API routes...
curl -f -s http://localhost:8000/api/users > api_response.json 2>nul
if %errorlevel% equ 0 (
    echo ✅ API users route accessible
) else (
    echo ❌ API users route failed
)

curl -f -s "http://localhost:8000/api/hello/test" | findstr "Hello, test" >nul 2>nul
if %errorlevel% equ 0 (
    echo ✅ API hello route working
) else (
    echo ❌ API hello route failed
)

REM Test 404 handling
echo.
echo Testing error handling...
for /f %%i in ('curl -s -o nul -w "%%{http_code}" http://localhost:8000/nonexistent 2^>nul') do set HTTP_CODE=%%i
if "%HTTP_CODE%"=="404" (
    echo ✅ 404 error handling works
) else (
    echo ❌ 404 error handling failed (got %HTTP_CODE%)
)

REM Clean up
echo.
echo 7. Cleaning up...
taskkill /f /im php.exe >nul 2>&1
del server.log 2>nul
del response.html 2>nul
del api_response.json 2>nul
echo ✅ Test cleanup completed

echo.
echo 🎉 All tests completed!
echo.
echo To manually test the framework:
echo   php -S localhost:8000 -t public/
echo   Then visit: http://localhost:8000/
pause
