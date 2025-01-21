<?php
declare(strict_types=1);
namespace Transport;

use PDO;
use PDOException;

class Database
{
	private static $instance = null;

	public PDO $dbh;
	public $sth;
	public mixed $errorInfo = null;

	/**
	 * In most instances, we want to use this a singleton, so that we're only 
	 * making a single connection to the database.
	 * However, we're keeping the constructor public so that we have the option
	 * of creating additional instances (connections) if we need to.
	 */

	public function __construct(
		?string $dbuser = null,
		?string $dbpassword = null,
		?string $dbname = null,
		?string $dbhost = null
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


	public static function getInstance(
		?string $dbuser = null,
		?string $dbpassword = null,
		?string $dbname = null,
		?string $dbhost = null
	): Database
	{
		if (self::$instance === null) {
			self::$instance = new self($dbuser, $dbpassword, $dbname, $dbhost);
		}
		return self::$instance;
	}

	public function start(): void
	{
		$this->dbh->beginTransaction();
	}

	public function cancel(): void
	{
		$this->dbh->rollBack();
	}

	public function complete(): void
	{
		$this->dbh->commit();
	}

	public function get_rows($query = null, $params = null, $fetch_mode = PDO::FETCH_OBJ): array
	{
		return $this->get_results($query, $params, $fetch_mode);
	}

	public function get_results($query = null, $params = null, $fetch_mode = PDO::FETCH_OBJ): array
	{
		$this->errorInfo = null;
		if (isset($query)) {
			$this->sth = $this->dbh->prepare($query);
		}
		$this->sth->setFetchMode($fetch_mode);
		if (is_array($params)) {
			$this->sth->execute($params);
		} else {
			$this->sth->execute();
		}
		$this->errorInfo = $this->sth->errorInfo();
		$rows = $this->sth->fetchAll();
		return $rows;
	}

	public function get_row($query = null, $params = null): object | false
	{
		if ($tmp = $this->get_results($query, $params)) {
			return $tmp[0];
		}
		return false;
	}

	public function get_var($query, $params = null): mixed
	{
		$this->errorInfo = null;
		if (isset($query)) {
			$this->sth = $this->dbh->prepare($query);
		}
		$this->sth->setFetchMode(PDO::FETCH_NUM);
		if (is_array($params)) {
			$this->sth->execute($params);
		} else {
			$this->sth->execute();
		}
		$this->errorInfo = $this->sth->errorInfo();
		$rows = $this->sth->fetchAll();
		return $rows[0][0];
	}

	public function prep($query): void
	{
		$this->sth = $this->dbh->prepare($query);
	}

	public function query($query = null, $params = null): int | true
	{
		$this->errorInfo = null;
		if (is_array($params)) {
			if (isset($query)) {
				$this->sth = $this->dbh->prepare($query);
			}
			$this->sth->execute($params);
			$this->errorInfo = $this->sth->errorInfo();
			if (preg_match('/insert /', strtolower($query))) {
				return (int)$this->dbh->lastInsertId();
			}
			return true;
		}
		$affected = $this->dbh->exec($query);
		if (preg_match('/insert /', strtolower($query))) {
			return (int)$this->dbh->lastInsertId();
		}
		return $affected;
	}
}
