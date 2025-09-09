<?php
namespace Craft\Database\QueryBuilder;

class SqliteBuilder extends BaseBuilder {
    public function toSql(): string {
        $sql = "SELECT " . implode(',', $this->columns) . " FROM \"{$this->table}\"";
        if ($this->wheres) {
            $where = array_map(function($w) {
                return "\"{$w[0]}\" {$w[1]} ?";
            }, $this->wheres);
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        return $sql;
    }
    // Cài insert, update, delete tương tự
}