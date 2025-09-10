<?php
namespace App\Model;

use Craft\Database\DatabaseManager;

class Model extends DatabaseManager
{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->mapper = $this->getMapper($this->table);
    }

    public function __call($method, $args)
    {
        /*
         * Forward các hàm CRUD cho mapper
         */
        return call_user_func_array([$this->mapper, $method], $args);
    }

    public static function __callStatic($method, $args)
    {
        /*
         * Forward các hàm CRUD cho mapper
         */
        $instance = new static();
        return call_user_func_array([$instance->mapper, $method], $args);
    }

    public function getAdapter()
    {
        return $this->adapter;
    }
}