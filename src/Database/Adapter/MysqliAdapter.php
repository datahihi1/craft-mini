<?php
namespace Craft\Database\Adapter;

use Craft\Database\Interface\AdapterInterface;

class MysqliAdapter implements AdapterInterface{
	protected $conn;

	public function connect(array $config)
	{
		$this->conn = new \mysqli(
			$config['host'] ?? 'localhost',
			$config['user'] ?? 'root',
			$config['password'] ?? '',
			$config['database'] ?? '',
			$config['port'] ?? 3306
		);
		if ($this->conn->connect_error) {
			throw new \Exception('MySQLi connect error: ' . $this->conn->connect_error);
		}
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
			case 'num': return $result->fetch_array(MYSQLI_NUM);
			case 'both': return $result->fetch_array(MYSQLI_BOTH);
			case 'object': return $result->fetch_object();
			case 'assoc':
			default: return $result->fetch_assoc();
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

	public function lastInsertId()
	{
		return $this->conn->insert_id;
	}

	public function beginTransaction()
	{
		$this->conn->begin_transaction();
	}

	public function commit()
	{
		$this->conn->commit();
	}

	public function rollback()
	{
		$this->conn->rollback();
	}

	public function getError()
	{
		return $this->conn ? $this->conn->error : null;
	}
}