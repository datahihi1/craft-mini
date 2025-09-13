#!/bin/bash

# CraftMini Framework Test Runner
# This script runs comprehensive tests on the framework

echo "🧪 CraftMini Framework Test Runner"
echo "=================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    if [ $2 -eq 0 ]; then
        echo -e "${GREEN}✅ $1${NC}"
    else
        echo -e "${RED}❌ $1${NC}"
    fi
}

# Check if PHP is available
echo "1. Checking PHP installation..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2)
    print_status "PHP $PHP_VERSION is installed" 0
else
    print_status "PHP is not installed" 1
    exit 1
fi

# Check if Composer is available
echo ""
echo "2. Checking Composer installation..."
if command -v composer &> /dev/null; then
    print_status "Composer is installed" 0
else
    print_status "Composer is not installed" 1
    exit 1
fi

# Install dependencies
echo ""
echo "3. Installing dependencies..."
composer install --no-progress --prefer-dist --optimize-autoloader
if [ $? -eq 0 ]; then
    print_status "Dependencies installed successfully" 0
else
    print_status "Failed to install dependencies" 1
    exit 1
fi

# Run basic framework test
echo ""
echo "4. Running framework tests..."
php test-framework.php
if [ $? -eq 0 ]; then
    print_status "Framework tests passed" 0
else
    print_status "Framework tests failed" 1
    exit 1
fi

# Create test environment
echo ""
echo "5. Setting up test environment..."
mkdir -p public/logs
touch public/test_manga_readers.db
chmod 666 public/test_manga_readers.db
echo "APP_ENVIRONMENT=testing" > .env
echo "APP_DEBUG=true" >> .env
print_status "Test environment created" 0

# Start web server and test routes
echo ""
echo "6. Testing web routes..."
echo "Starting PHP built-in server..."

# Start server in background
php -S localhost:8000 -t public/ > server.log 2>&1 &
SERVER_PID=$!

# Wait for server to start
sleep 3

# Test default route
echo "Testing default route (/)..."
if curl -f -s http://localhost:8000/ > response.html 2>/dev/null; then
    if grep -q "Xin chào" response.html; then
        print_status "Default route returns expected content" 0
    else
        print_status "Default route content mismatch" 1
        echo "Response content:"
        cat response.html
    fi
else
    print_status "Default route not accessible" 1
    echo "Server log:"
    cat server.log
fi

# Test API routes
echo ""
echo "Testing API routes..."
if curl -f -s http://localhost:8000/api/users > api_response.json 2>/dev/null; then
    print_status "API users route accessible" 0
else
    print_status "API users route failed" 1
fi

if curl -f -s "http://localhost:8000/api/hello/test" | grep -q "Hello, test" 2>/dev/null; then
    print_status "API hello route working" 0
else
    print_status "API hello route failed" 1
fi

# Test 404 handling
echo ""
echo "Testing error handling..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/nonexistent 2>/dev/null)
if [ "$HTTP_CODE" = "404" ]; then
    print_status "404 error handling works" 0
else
    print_status "404 error handling failed (got $HTTP_CODE)" 1
fi

# Clean up
echo ""
echo "7. Cleaning up..."
kill $SERVER_PID 2>/dev/null || true
rm -f server.log response.html api_response.json

print_status "Test cleanup completed" 0

echo ""
echo "🎉 All tests completed!"
echo ""
echo "To manually test the framework:"
echo "  php -S localhost:8000 -t public/"
echo "  Then visit: http://localhost:8000/"
