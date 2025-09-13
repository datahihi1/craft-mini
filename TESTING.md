# CraftMini Framework Testing

This document describes the comprehensive testing setup for the CraftMini Framework, including GitHub Actions workflows and local testing scripts.

## 🚀 GitHub Actions Workflow

### File: `.github/workflows/test-framework.yml`

This workflow automatically tests the framework on:

**Operating Systems:**
- ✅ Ubuntu Latest (Linux)
- ✅ Windows Latest  
- ✅ macOS Latest

**PHP Versions:**
- ✅ PHP 7.1 (minimum supported)
- ✅ PHP 7.4
- ✅ PHP 8.0
- ✅ PHP 8.1
- ✅ PHP 8.2
- ✅ PHP 8.3

**Test Coverage:**
- ✅ Framework startup and initialization
- ✅ Default route (`/`) accessibility and content
- ✅ API routes functionality (`/api/users`, `/api/hello/{name}`)
- ✅ Database connectivity (SQLite)
- ✅ Error handling (404 responses)
- ✅ Composer autoloading
- ✅ Core framework components
- ✅ Hash functionality (bcrypt, argon2i)
- ✅ Session and flash message handling

## 🧪 Local Testing

### 1. Basic Framework Test
```bash
php test-framework.php
```

Tests:
- PHP version compatibility (≥7.1)
- Required extensions (json, mysqli, pdo, pdo_sqlite)
- Composer autoloader
- Core classes availability
- File structure integrity
- Database connectivity
- Framework initialization

### 2. Comprehensive Test Suite

**Linux/macOS:**
```bash
./run-tests.sh
```

**Windows:**
```cmd
run-tests.bat
```

Tests:
- All basic framework tests
- Web server startup
- Default route content verification
- API routes functionality
- Error handling (404 responses)
- Automatic cleanup

### 3. Manual Testing

Start the development server:
```bash
php -S localhost:8000 -t public/
```

Test these URLs:
- `http://localhost:8000/` - Default route (Vietnamese welcome + hash tests)
- `http://localhost:8000/api/users` - Users API
- `http://localhost:8000/api/hello/test` - Hello API
- `http://localhost:8000/nonexistent` - 404 error handling

## 📋 Expected Behavior

### Default Route (`/`)
- Returns Vietnamese welcome message: "Xin chào, đây là trang chủ..."
- Displays hash test results (default, bcrypt, argon2i)
- Shows flash message with random code
- Demonstrates session functionality

### API Routes
- `GET /api/users` - Returns user data array
- `GET /api/hello/{name}` - Returns "Hello, {name}" message
- Supports proper HTTP methods (GET, POST, PUT, DELETE)

### Error Handling
- Non-existent routes return 404 status
- Proper error logging to `public/logs/`

## 🔧 Configuration

### Environment Variables
Create `.env` file:
```
APP_ENVIRONMENT=testing
APP_DEBUG=true
```

### Database
- Uses SQLite database: `public/manga_readers.db`
- Test database: `public/test_manga_readers.db`

### Dependencies
- PHP ≥7.1
- Composer
- Required extensions: json, mysqli, pdo, pdo_sqlite

## 📁 Files Created

- `.github/workflows/test-framework.yml` - GitHub Actions workflow
- `.github/README.md` - GitHub Actions documentation
- `test-framework.php` - Basic framework test script
- `run-tests.sh` - Comprehensive test script (Linux/macOS)
- `run-tests.bat` - Comprehensive test script (Windows)
- `TESTING.md` - This documentation
- Updated `.gitignore` - Excludes test artifacts

## 🎯 Testing Strategy

1. **Unit Tests**: Individual component testing
2. **Integration Tests**: Framework startup and routing
3. **End-to-End Tests**: Full HTTP request/response cycle
4. **Cross-Platform Tests**: Multiple OS and PHP versions
5. **Error Handling Tests**: 404 responses and error logging

## 🚨 Troubleshooting

### Common Issues

1. **Composer not found**: Install Composer globally
2. **PHP extensions missing**: Install required PHP extensions
3. **Database permission errors**: Check file permissions on database files
4. **Port 8000 in use**: Use different port or kill existing process

### Debug Mode

Enable debug mode in `.env`:
```
APP_DEBUG=true
```

Check logs in `public/logs/` for detailed error information.

## 📊 Test Results

The GitHub Actions workflow will show:
- ✅ Green checkmarks for passed tests
- ❌ Red X marks for failed tests
- Detailed logs for debugging failures
- Test artifacts uploaded on failure

Each test matrix combination (OS × PHP version) runs independently, so you can see exactly which combinations pass or fail.
