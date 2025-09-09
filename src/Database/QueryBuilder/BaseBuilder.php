<?php
namespace Craft\Database\QueryBuilder;

use Craft\Database\Interface\BuilderInterface;

abstract class BaseBuilder implements BuilderInterface {
    protected $table;
    protected $columns = ['*'];
    protected $wheres = [];
    protected $bindings = [];

    public function table(string $table): self {
        $this->table = $table;
        return $this;
    }

    public function select($columns = ['*']): self {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function where($column, $operator = null, $value = null): self {
        $this->wheres[] = [$column, $operator, $value];
        $this->bindings[] = $value;
        return $this;
    }

    public function getBindings(): array {
        return $this->bindings;
    }

    // insert/update/delete/toSql sẽ được cài ở từng builder con
}