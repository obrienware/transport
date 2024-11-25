<?php

class data
{
	private $prep_stmt;

	public $dbh;
	public $sth;
	public $errorInfo = null;

	public function __construct(
		$dbuser = null,
		$dbpassword = null,
		$dbname = null,
		$dbhost = null
	) {
		if (!isset($dbuser)) {
			$dbuser = $_ENV['DB_USER'];
		}
		if (!isset($dbpassword)) {
			$dbpassword = $_ENV['DB_PASS'];
		}
		if (!isset($dbname)) {
			$dbname = $_ENV['DB_DATABASE'];
		}
		if (!isset($dbhost)) {
			$dbhost = $_ENV['DB_HOST'];
		}

		try {
			$this->dbh = new PDO(
				'mysql:host=' . $dbhost . ';dbname=' . $dbname,
				$dbuser,
				$dbpassword
			);
		} catch (PDOException $e) {
			die('Failed: ' . $e->getMessage());
		}
	}

	public function start()
	{
		$this->dbh->beginTransaction();
	}

	public function cancel()
	{
		$this->dbh->rollBack();
	}

	public function complete()
	{
		$this->dbh->commit();
	}

	public function get_results($sql = null, $params = null, $fetch_mode = PDO::FETCH_OBJ)
	{
		$this->errorInfo = null;
		if (isset($sql)) {
			$this->sth = $this->dbh->prepare($sql);
		}
		$this->sth->setFetchMode($fetch_mode);
		if (is_array($params)) {
			$this->sth->execute($params);
		} else {
			$this->sth->execute();
		}
		$this->errorInfo = $this->sth->errorInfo();
		$rs = $this->sth->fetchAll();
		return $rs;
	}

	public function get_row($sql = null, $params = null)
	{
		if ($tmp = $this->get_results($sql, $params)) {
			return $tmp[0];
		}
		return false;
	}

	public function get_var($sql, $params = null)
	{
		$this->errorInfo = null;
		if (isset($sql)) {
			$this->sth = $this->dbh->prepare($sql);
		}
		$this->sth->setFetchMode(PDO::FETCH_NUM);
		if (is_array($params)) {
			$this->sth->execute($params);
		} else {
			$this->sth->execute();
		}
		$this->errorInfo = $this->sth->errorInfo();
		$rs = $this->sth->fetchAll();
		return $rs[0][0];
	}

	public function prep($sql)
	{
		$this->sth = $this->dbh->prepare($sql);
	}

	public function query($sql = null, $params = null)
	{
		$this->errorInfo = null;
		if (is_array($params)) {
			if (isset($sql)) {
				$this->sth = $this->dbh->prepare($sql);
			}
			$this->sth->execute($params);
			$this->errorInfo = $this->sth->errorInfo();
			if (preg_match('/insert /', strtolower($sql))) {
				return $this->dbh->lastInsertId();
			}
			return true;
		}
		$affected = $this->dbh->exec($sql);
		if (preg_match('/insert /', strtolower($sql))) {
			return $this->dbh->lastInsertId();
		}
		return $affected;
	}
}
