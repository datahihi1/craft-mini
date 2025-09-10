<?php
namespace Craft\Database;

use Craft\Database\Adapter\MysqliAdapter;
use Craft\Database\Adapter\PdoMysqlAdapter;
use Craft\Database\Adapter\Sqlite3Adapter;
use Craft\Database\Adapter\PdoSqliteAdapter;
use Craft\Database\Mapper\MysqlMapper;
use Craft\Database\Mapper\SqliteMapper;
use Craft\Database\QueryBuilder\MysqlBuilder;
use Craft\Database\QueryBuilder\SqliteBuilder;

/**
 * DatabaseManager class
 * 
 * Manages database connections and mappers/builders based on configuration.
 */
class DatabaseManager
{
    protected $adapter;
    protected $mapper;
    protected $builder;
    protected $mapperClass;
    protected $builderClass;

    public function __construct()
    {
        $driver = env('DB_DRIVER', 'pdo_mysql');
        $design = env('DB_DESIGN', 'mapper');
        $config = $this->getConfig($driver);

        // Chọn adapter theo driver
        switch ($driver) {
            case 'mysqli':
                $this->adapter = new MysqliAdapter();
                break;
            case 'sqlite3':
                $this->adapter = new Sqlite3Adapter();
                break;
            case 'pdo_sqlite':
                $this->adapter = new PdoSqliteAdapter();
                break;
            case 'pdo_mysql':
            default:
                $this->adapter = new PdoMysqlAdapter();
        }
        $this->adapter->connect($config);

        // Chọn lớp thi hành theo thiết kế (mapper hoặc builder)
        if ($design === 'mapper') {
            if (in_array($driver, ['mysqli', 'pdo_mysql'])) {
                $this->mapperClass = MysqlMapper::class;
            } else if (in_array($driver, ['sqlite3', 'pdo_sqlite'])) {
                $this->mapperClass = SqliteMapper::class;
            }
        } else if ($design === 'builder') {
            if (in_array($driver, ['mysqli', 'pdo_mysql'])) {
                $this->mapperClass = MysqlBuilder::class;
            } else if (in_array($driver, ['sqlite3', 'pdo_sqlite'])) {
                $this->mapperClass = SqliteBuilder::class;
            }
        }
    }

    /**
     * Get database configuration based on driver
     * @param mixed $driver
     * @return array
     */
    protected function getConfig($driver)
    {
        if (strpos($driver, 'sqlite') !== false) {
            return [
                // Thống nhất key 'database' cho các adapter PDO Sqlite/Sqlite3
                'database' => env('DB_SQLITE_FILE'),
            ];
        }
        return [
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'user' => env('DB_USER'),
            // Thống nhất key 'password' thay vì 'pass'
            'password' => env('DB_PASS') ?? null,
            'database' => env('DB_NAME'),
        ];
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function getMapper($table)
    {
        // Với 'mapper': lớp Mapper nhận ($adapter, $table)
        // Với 'builder': Builder cũng sẽ nhận ($adapter, $table) và tự thực thi thông qua adapter
        return new $this->mapperClass($this->adapter, $table);
    }
}