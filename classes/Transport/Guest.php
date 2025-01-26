<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class Guest extends	Base
{
	protected $tableName = 'guests';
	protected $tableDescription = 'Guests';

	public ?string $firstName = null;
	public ?string $lastName = null;
	public ?string $emailAddress = null;
	public ?string $phoneNumber = null;

	public function load(int $id): bool
	{
		$db = Database::getInstance();
		$query = "SELECT * FROM {$this->tableName} WHERE id = :id";
		$params = ['id' => $id];
		if ($row = $db->get_row($query, $params)) {
			$this->mapRowToProperties($row);
			return true;
		}
		return false;
	}

	protected function mapRowToProperties(object $row): void
	{
		$defaultTimezone = new DateTimeZone($_ENV['TZ'] ?? 'UTC');
    $this->row = $row;
    $this->action = 'update';

		$this->id = $row->id;
		$this->firstName = $row->first_name;
		$this->lastName = $row->last_name;
		$this->emailAddress = $row->email_address;
		$this->phoneNumber = $row->phone_number;

		if (!empty($row->archived)) {
			$this->archived = (new DateTime($row->archived, $defaultTimezone))->setTimezone($this->timezone);
		}
	}

	public function save(string $userResponsibleForOperation = null): bool
	{
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);

		$params = [
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'email_address' => $this->emailAddress,
			'phone_number' => $this->phoneNumber ? Utils::formattedPhoneNumber($this->phoneNumber) : null,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE {$this->tableName} SET
					first_name = :first_name,
					last_name = :last_name,
					email_address = :email_address,
					phone_number = :phone_number,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = $this->tableDescription.' created: '.$this->getName();
			$query = "
				INSERT INTO {$this->tableName} SET
					first_name = :first_name,
					last_name = :last_name,
					email_address = :email_address,
					phone_number = :phone_number,
					created = NOW(),
					created_by = :user
			";
		}
		try {
			$result = $db->query($query, $params);
			$id = ($this->action === 'create') ? $result : $this->id;
			$this->load($id);
			$audit->after = json_encode($this->row);
			$audit->commit();
			return true;
		} catch (\Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
	}

	static function getAll(): array
	{
		$db = Database::getInstance();
		$query = "
			SELECT g.*, o.opt_in, o.opt_out FROM guests g
			LEFT OUTER JOIN opt_in_text o ON o.tel = g.phone_number
			WHERE g.archived IS NULL 
			ORDER BY g.first_name, g.last_name
		";
		return $db->get_rows($query);
	}

	protected function reset(): void
	{
		parent::reset();

		$this->firstName = null;
		$this->lastName = null;
		$this->emailAddress = null;
		$this->phoneNumber = null;
	}

	public function getGuestByPhoneNumber(string $phoneNumber): bool
	{
		$db = Database::getInstance();
		$query = 'SELECT * FROM guests WHERE phone_number = :phone_number AND archived IS NULL';
		$params = ['phone_number' => $phoneNumber];
		if ($row = $db->get_row($query, $params)) {
			$this->mapRowToProperties($row);
			return true;
		}
		return false;
	}

	public function getName(): string
	{
		return "{$this->firstName} {$this->lastName}";
	}

}
