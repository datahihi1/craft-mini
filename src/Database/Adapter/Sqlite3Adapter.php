<?php
namespace Craft\Database\Adapter;

use Craft\Database\Interface\AdapterInterface;

class Sqlite3Adapter implements AdapterInterface{
	protected $conn;

	public function connect(array $config)
	{
		$this->conn = new \SQLite3($config['database'] ?? ':memory:');
	}

	public function disconnect()
	{
		if ($this->conn) {
			$this->conn->close();
		}
	}

	public function query($sql, $params = [])
	{
		// Đơn giản hóa, chưa bind param
		return $this->conn->query($sql);
	}

    public function fetch($result, $type = 'assoc')
    {
        switch ($type) {
            case 'num': return $result->fetchArray(SQLITE3_NUM);
            case 'both': return $result->fetchArray(SQLITE3_BOTH);
            case 'object': return $result->fetchObject();
            case 'assoc':
            default: return $result->fetchArray(SQLITE3_ASSOC);
        }
    }

    public function fetchAll($result, $type = 'assoc')
    {
        $data = [];
        while ($row = $this->fetch($result, $type)) {
            $data[] = $row;
        }
        return $data;
    }

    public function getError()
    {
        return $this->conn ? $this->conn->lastErrorMsg() : null;
    }
	public function lastInsertId()
	{
		return $this->conn->lastInsertRowID();
	}

	public function beginTransaction()
	{
		$this->conn->exec('BEGIN');
	}

	public function commit()
	{
		$this->conn->exec('COMMIT');
	}

	public function rollback()
	{
		$this->conn->exec('ROLLBACK');
	}
}