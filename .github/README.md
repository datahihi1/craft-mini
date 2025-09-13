# GitHub Actions Testing

This directory contains GitHub Actions workflows for testing the CraftMini Framework.

## Workflows

### test-framework.yml

Tests the CraftMini Framework across multiple operating systems and PHP versions.

**Operating Systems:**
- Ubuntu Latest (Linux)
- Windows Latest
- macOS Latest

**PHP Versions:**
- PHP 7.1 (minimum supported version)
- PHP 7.4
- PHP 8.0
- PHP 8.1
- PHP 8.2
- PHP 8.3

**Test Coverage:**
- ✅ Framework startup and initialization
- ✅ Default route (`/`) accessibility
- ✅ API routes functionality
- ✅ Database connectivity (SQLite)
- ✅ Error handling (404 responses)
- ✅ Composer autoloading
- ✅ Core framework components
- ✅ Hash functionality (bcrypt, argon2i)
- ✅ Session and flash message handling

## Local Testing

You can test the framework locally using the provided test script:

```bash
php test-framework.php
```

This will run basic checks for:
- PHP version compatibility
- Required extensions
- Composer autoloader
- Core classes availability
- File structure
- Database connectivity
- Framework initialization

## Manual Testing

To manually test the framework:

1. Install dependencies:
   ```bash
   composer install
   ```

2. Start the development server:
   ```bash
   php -S localhost:8000 -t public/
   ```

3. Visit the default route:
   ```
   http://localhost:8000/
   ```

4. Test API routes:
   ```
   http://localhost:8000/api/users
   http://localhost:8000/api/hello/test
   ```

## Expected Behavior

The default route (`/`) should:
- Return a Vietnamese welcome message
- Display hash test results (default, bcrypt, argon2i)
- Show a flash message with a random code
- Demonstrate session functionality

The API routes should:
- `/api/users` - Return user data (GET)
- `/api/hello/{name}` - Return "Hello, {name}" message
- Support proper HTTP methods (GET, POST, PUT, DELETE)
