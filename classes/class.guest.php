<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';


class Guest
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;

	public $firstName;
	public $lastName;
	public $emailAddress;
	public $phoneNumber;

	public function __construct(int $id = null)
	{
		$this->db = new data();
		if (isset($id)) $this->load($id);
	}


	public function load(int $id): bool
	{
		$query = 'SELECT * FROM guests WHERE id = :id';
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
			$this->firstName = $row->first_name;
			$this->lastName = $row->last_name;
			$this->emailAddress = $row->email_address;
			$this->phoneNumber = $row->phone_number;
			$this->archived = $row->archived;
			return true;
		}
		return false;
	}


	public function getId(): int | null
	{
		return $this->id;
	}


	public function save(string $userResponsibleForOperation = null): bool
	{
		$this->lastError = null;
		$audit = new Audit();
		$audit->user = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->table = 'guests';
		$audit->before = json_encode($this->row);

		$params = [
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'email_address' => $this->emailAddress,
			'phone_number' => $this->phoneNumber,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = 'Guest updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE guests SET
					first_name = :first_name,
					last_name = :last_name,
					email_address = :email_address,
					phone_number = :phone_number,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = 'Guest created: '.$this->getName();
			$query = "
				INSERT INTO guests SET
					first_name = :first_name,
					last_name = :last_name,
					email_address = :email_address,
					phone_number = :phone_number,
					created = NOW(),
					created_by = :user
			";
		}
		try {
			$result = $this->db->query($query, $params);
			$id = ($this->action === 'create') ? $result : $this->id;
			$this->load($id);
			$audit->after = json_encode($this->row);
			$audit->commit();
			return true;
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
	}


	public function delete(string $userResponsibleForOperation = null): bool
	{
		$this->lastError = null;
		$audit = new Audit();
		$audit->user = $userResponsibleForOperation;
		$audit->action = 'delete';
		$audit->table = 'guests';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE guests 
			SET 
				archived = NOW(), archived_by = :user 
			WHERE id = :id
		";
		$params = [
			'user' => $userResponsibleForOperation, 
			'id' => $this->id
		];
		try {
			$this->db->query($query, $params);
			$audit->description = 'Guest deleted: '.$this->getName();
			$audit->commit();
			$this->reset();
			return true;	
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
	}


	static function getAll(): array
	{
		$db = new data();
		$query = "
			SELECT g.*, o.opt_in, o.opt_out FROM guests g
			LEFT OUTER JOIN opt_in_text o ON o.tel = g.phone_number
			WHERE g.archived IS NULL 
			ORDER BY g.first_name, g.last_name
		";
		return $db->get_rows($query);
	}


	public function isArchived(): bool
	{
		return isset($this->archived);
	}


	private function reset(): void
	{
		$this->id = null;
		$this->row = null;
		$this->lastError = null;
		$this->action = 'create';
		$this->archived = null;

		$this->firstName = null;
		$this->lastName = null;
		$this->emailAddress = null;
		$this->phoneNumber = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
	}


	public function getLastError(): string | null
	{
		return $this->lastError;
	}


	public function getGuestByPhoneNumber(string $phoneNumber): bool
	{
		$query = 'SELECT * FROM guests WHERE phone_number = :phone_number';
		$params = ['phone_number' => $phoneNumber];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
			$this->firstName = $row->first_name;
			$this->lastName = $row->last_name;
			$this->emailAddress = $row->email_address;
			$this->phoneNumber = $row->phone_number;
			$this->archived = $row->archived;
			return true;
		}
		return false;
	}


	public function getName(): string
	{
		return "{$this->firstName} {$this->lastName}";
	}

}
