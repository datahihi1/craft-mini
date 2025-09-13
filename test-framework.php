<?php
/**
 * Local test script for CraftMini Framework
 * Run with: php test-framework.php
 */

echo "🧪 Testing CraftMini Framework...\n\n";

// Test 1: Check PHP version
echo "1. PHP Version Check:\n";
$phpVersion = PHP_VERSION;
$minVersion = '7.1.0';
if (version_compare($phpVersion, $minVersion, '>=')) {
    echo "   ✅ PHP $phpVersion (meets minimum requirement $minVersion)\n";
} else {
    echo "   ❌ PHP $phpVersion (below minimum requirement $minVersion)\n";
    exit(1);
}

// Test 2: Check required extensions
echo "\n2. Required Extensions Check:\n";
$requiredExtensions = ['json', 'mysqli', 'pdo', 'pdo_sqlite'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ $ext extension loaded\n";
    } else {
        echo "   ❌ $ext extension not loaded\n";
        exit(1);
    }
}

// Test 3: Check Composer autoloader
echo "\n3. Composer Autoloader Check:\n";
$autoloadFile = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadFile)) {
    require_once $autoloadFile;
    echo "   ✅ Composer autoloader loaded\n";
} else {
    echo "   ❌ Composer autoloader not found. Run 'composer install'\n";
    exit(1);
}

// Test 4: Check core classes
echo "\n4. Core Classes Check:\n";
$coreClasses = [
    'Craft\\Application\\App',
    'Craft\\Application\\Router',
    'App\\Controller\\HomeController',
    'App\\Controller\\Controller'
];

foreach ($coreClasses as $class) {
    if (class_exists($class)) {
        echo "   ✅ $class loaded\n";
    } else {
        echo "   ❌ $class not found\n";
        exit(1);
    }
}

// Test 5: Check file structure
echo "\n5. File Structure Check:\n";
$requiredFiles = [
    'public/index.php',
    'src/Application/App.php',
    'app/Controller/HomeController.php',
    'app/Router/web.php',
    'resource/view/home.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ $file exists\n";
    } else {
        echo "   ❌ $file missing\n";
        exit(1);
    }
}

// Test 6: Test database connection
echo "\n6. Database Connection Test:\n";
try {
    $pdo = new PDO('sqlite:public/manga_readers.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✅ SQLite database connection successful\n";
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 7: Test framework initialization
echo "\n7. Framework Initialization Test:\n";
try {
    // Set up environment
    if (!defined('ROOT_DIR')) {
        define('ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR);
    }
    if (!defined('INDEX_DIR')) {
        define('INDEX_DIR', __DIR__ . '/public' . DIRECTORY_SEPARATOR);
    }
    
    // Test App class methods exist
    if (method_exists('Craft\\Application\\App', 'initializeWeb')) {
        echo "   ✅ App::initializeWeb method exists\n";
    } else {
        echo "   ❌ App::initializeWeb method not found\n";
        exit(1);
    }
    
    if (method_exists('Craft\\Application\\App', 'bootWeb')) {
        echo "   ✅ App::bootWeb method exists\n";
    } else {
        echo "   ❌ App::bootWeb method not found\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "   ❌ Framework initialization failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 8: Test routing
echo "\n8. Routing Test:\n";
try {
    $router = new Craft\Application\Router();
    echo "   ✅ Router instantiated successfully\n";
    
    // Test that routes are defined
    $webRoutes = include 'app/Router/web.php';
    echo "   ✅ Web routes loaded successfully\n";
    
} catch (Exception $e) {
    echo "   ❌ Routing test failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 All tests passed! CraftMini Framework is ready to use.\n";
echo "\nTo start the development server, run:\n";
echo "   php -S localhost:8000 -t public/\n";
echo "\nThen visit: http://localhost:8000/\n";
