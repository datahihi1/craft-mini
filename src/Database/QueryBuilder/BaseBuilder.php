<?php
namespace Craft\Database\QueryBuilder;

use Craft\Database\Interfaces\BuilderInterface;

abstract class BaseBuilder implements BuilderInterface {
    protected $adapter;
    protected $table;
    protected $columns = ['*'];
    protected $wheres = [];
    protected $bindings = [];

    public function __construct($adapter, string $table)
    {
        $this->adapter = $adapter;
        $this->table = $table;
    }

    public function table(string $table): BuilderInterface {
        $this->table = $table;
        return $this;
    }

    public function select($columns = ['*']): BuilderInterface {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function where($column, $operator = null, $value = null): BuilderInterface {
        $this->wheres[] = [$column, $operator, $value];
        $this->bindings[] = $value;
        return $this;
    }

    public function getBindings(): array {
        return $this->bindings;
    }

    // insert/update/delete/toSql sẽ được cài ở từng builder con

    /**
     * Thực thi câu lệnh hiện tại và trả về toàn bộ bản ghi
     */
    public function fetchAll(): array
    {
        $sql = $this->toSql();
        $params = $this->getBindings();
        $result = $this->adapter->query($sql, $params);
        return $this->adapter->fetchAll($result);
    }

    /**
     * Thực thi và trả về một bản ghi
     */
    public function fetch(string $type = 'assoc')
    {
        $sql = $this->toSql();
        $params = $this->getBindings();
        $result = $this->adapter->query($sql, $params);
        return $this->adapter->fetch($result, $type);
    }

    /**
     * Alias của fetch() nhưng tiện dụng hơn
     */
    public function first(string $type = 'assoc')
    {
        $rows = $this->fetchAll();
        return $rows[0] ?? null;
    }

    /**
     * Alias của fetchAll()
     */
    public function get(): array
    {
        return $this->fetchAll();
    }

    /**
     * Thực thi INSERT và trả về ID vừa tạo (nếu hỗ trợ)
     */
    public function insertGetId(array $data)
    {
        $sql = $this->insert($data);
        $params = array_values($data);
        $this->adapter->query($sql, $params);
        if (method_exists($this->adapter, 'lastInsertId')) {
            return $this->adapter->lastInsertId();
        }
        return null;
    }

    /**
     * Thực thi UPDATE, trả về true nếu thành công
     */
    public function executeUpdate(array $data)
    {
        $sql = $this->update($data);
        $params = array_values($data);
        $params = array_merge($params, $this->getBindings());
        return $this->adapter->query($sql, $params) ? true : false;
    }

    /**
     * Thực thi DELETE, trả về true nếu thành công
     */
    public function executeDelete()
    {
        $sql = $this->delete();
        $params = $this->getBindings();
        return $this->adapter->query($sql, $params) ? true : false;
    }
}