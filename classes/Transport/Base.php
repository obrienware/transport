<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

abstract class Base
{
  protected $tableName;
  protected $tableDescription;

  protected ?DateTimeZone $timezone = null;
  protected ?int $id = null;
  protected ?object $row = null;
  protected ?string $lastError = null;
  protected string $action = 'create';
  protected ?DateTime $archived = null;

  
  public function __construct(mixed $id = null)
  {
    $this->timezone = new DateTimeZone($_SESSION['userTimezone'] ?? 'UTC');
    if ($id) $this->load($id);
  }

  abstract public function load(int $id): bool;
  abstract public function getName(): string;
  abstract public function save(?string $userResponsibleForOperation = null): bool;
  abstract protected function mapRowToProperties(object $row): void;
  
  
  protected function reset(): void
  {
    $this->id = null;
    $this->row = null;
    $this->lastError = null;
    $this->action = 'create';
    $this->archived = null;
  }

  
  public function getId(): ?int
  {
    return $this->id;
  }

  
  public function getLastError(): ?string
  {
    return $this->lastError;
  }

  
  public function isArchived(): bool
  {
    return !empty($this->archived);
  }

  
  public function archived(): ?string
  {
    return $this->archived ? $this->archived->format('Y-m-d H:i:s') : null;
  }

  
  public function setTimezone(string $timezone): void
  {
    $this->timezone = new DateTimeZone($timezone);
  }

  
  public function delete(?string $userResponsibleForOperation = null): bool
  {
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = 'delete';
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE {$this->tableName} SET 
				archived = NOW(), archived_by = :user 
			WHERE id = :id
		";
		$params = [
			'user' => $userResponsibleForOperation, 
			'id' => $this->id
		];
		try {
			$db->query($query, $params);
			$audit->description = $this->tableDescription.' deleted: '.$this->getName();
			$audit->commit();
			$this->reset();
			return true;	
		} catch (\Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
  }

  
  public static function makeFromRows(array $rows): array | false
  {
    if (empty($rows)) return false;
    $className = get_called_class();
    $returnValue = [];
    foreach ($rows as $row) {
      $obj = new $className();
      $obj->mapRowToProperties($row);
      $returnValue[] = $obj;
    }
    return $returnValue;
  }

}
