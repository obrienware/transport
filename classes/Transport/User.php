<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class User extends Base
{
	protected $tableName = 'users';
	protected $tableDescription = 'Users';

	public ?string $username = null;
	public ?string $firstName = null;
	public ?string $lastName = null;
	public ?string $emailAddress = null;
	public ?string $phoneNumber = null;
	public array $roles = [];
	public ?string $position = null;
	public ?int $departmentId = null;
	public bool $CDL = false;
	public bool $changePassword = false;
	public ?string $preferences = null;

	public function getName(): string
	{
		if ($this->id) return "{$this->firstName} {$this->lastName}";
		return '';
	}

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
		$this->username = $row->username;
		$this->firstName = $row->first_name;
		$this->lastName = $row->last_name;
		$this->emailAddress = $row->email_address;
		$this->phoneNumber = $row->phone_number;
		$this->roles = $row->roles ? explode(',', $row->roles) : [];
		$this->position = $row->position;
		$this->departmentId = $row->department_id;
		$this->CDL = ($row->cdl == 1);
		$this->changePassword = ($row->change_password == 1);
		$this->preferences = $row->personal_preferences;

		if (!empty($row->archived)) {
			$this->archived = (new DateTime($row->archived, $defaultTimezone))->setTimezone($this->timezone);
		}
	}

	public function save(?string $userResponsibleForOperation = null): bool
	{
    $db = Database::getInstance();
		$this->lastError = null;
		$audit = new Audit();
		$audit->username = $userResponsibleForOperation;
		$audit->action = $this->action;
		$audit->tableName = $this->tableName;
		$audit->before = json_encode($this->row);

		$params = [
			'username' => $this->username,
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'email_address' => $this->emailAddress,
			'phone_number' => $this->phoneNumber,
			'roles' => $this->roles ? implode(',', $this->roles) : '',
			'position' => $this->position,
			'department_id' => $this->departmentId,
			'cdl' => $this->CDL ? 1 : 0,
			'personal_preferences' => $this->preferences ?? null,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = $this->tableDescription.' updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE {$this->tableName} SET
					username = :username,
					first_name = :first_name,
					last_name = :last_name,
					email_address = :email_address,
					phone_number = :phone_number,
					roles = :roles,
					position = :position,
					department_id = :department_id,
					cdl = :cdl,
					personal_preferences = :personal_preferences,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		} else {
			$audit->description = $this->tableDescription.' created: '.$this->getName();
			$query = "
				INSERT INTO {$this->tableName} SET
					username = :username,
					first_name = :first_name,
					last_name = :last_name,
					email_address = :email_address,
					phone_number = :phone_number,
					roles = :roles,
					position = :position,
					department_id = :department_id,
					cdl = :cdl,
					personal_preferences = :personal_preferences,
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

	protected function reset(): void
	{
		parent::reset();

		$this->username = null;
		$this->firstName = null;
		$this->lastName = null;
		$this->emailAddress = null;
		$this->phoneNumber = null;
		$this->roles = [];
		$this->position = null;
		$this->departmentId = null;
		$this->CDL = false;
		$this->changePassword = false;
		$this->preferences = null;
	}

	public function getUsername(): string
	{
		return $this->username ? $this->username : $this->emailAddress;
	}

	public function resetPassword(): bool
	{
		if ($this->emailAddress) {
			$db = Database::getInstance();
			$newPassword = Utils::randomPassword(10);
			$query = 'UPDATE users SET password = :password, change_password = 1 WHERE id = :user_id';
			$params = ['password' => md5($newPassword), 'user_id' => $this->id];
			$db->query($query, $params);

			$email = new Email();
			$email->setSubject('Your password has been reset.');
			$email->setContent("Your new temporary password has been set to\n\n{$newPassword}\n\nVisit https://{$_SERVER['HTTP_HOST']}");
			$email->addRecipient($this->emailAddress, $this->getName());
			$email->sendText();
			return true;
		}
		return false;
	}

	public static function sendResetLink(string $username) 
	{
		// First determine if we have a user that matches the given username
		$db = Database::getInstance();
		$query = "SELECT id FROM users WHERE username = :username AND archived IS NULL";
		$params = ['username' => $username];
		$id = $db->get_var($query, $params);
		if ($id) {
			$tmpUser = new User($id);
			$token = $tmpUser->setPasswordToken();

			$email = new Email();
			$email->setSubject('Password reset.');
			$email->setContent("To reset your password, please navigate to the following link : https://{$_SERVER['HTTP_HOST']}/reset/{$token}");
			$email->addRecipient($tmpUser->emailAddress, $tmpUser->firstName);
			return $email->sendText();
		}
		return false;
	}


	public function setPasswordToken(): string
	{
		$db = Database::getInstance();
		$query = "
			UPDATE users SET 
				reset_token = :token, token_expiration = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
			WHERE id = :user_id
		";
		$params = [
			'token' => bin2hex(random_bytes(10 / 2)),
			'user_id' => $this->id
		];
		$db->query($query, $params);
		return $params['token'];
	}


	public function setNewPassword($newPassword)
	{
		$db = Database::getInstance();
		$query = 'UPDATE users SET password = :new_password, change_password = 0, reset_token = NULL, token_expiration = NULL WHERE id = :id';
		$params = ['new_password' => md5($newPassword), 'id' => $this->id];
		return $db->query($query, $params);
	}


	public static function getDrivers(): array
	{
		return self::getUsersByRole('driver');
	}


	public static function getManagers(): array
	{
		return self::getUsersByRole('manager');
	}


	public static function getUsersByRole(string $role): array | false
	{
		$db = Database::getInstance();
		$query = "
			SELECT * FROM users 
			WHERE 
				FIND_IN_SET(:role, roles)
				AND archived IS NULL
			ORDER BY first_name, last_name
		";
		$params = ['role' => $role];
		return $db->get_rows($query, $params);
	}


	public static function getUsers(): array | false
	{
		$db = Database::getInstance();
		$query = "
			SELECT u.*, d.name AS department FROM users u 
			LEFT OUTER JOIN departments d ON d.id = u.department_id
			WHERE u.archived IS NULL ORDER BY first_name, last_name
		";
		return $db->get_rows($query);
	}


	public function getUserByEmail($emailAddress): bool
	{
		$db = Database::getInstance();
		$query = "SELECT * FROM users WHERE email_address = :email_address AND archived IS NULL";
		$params = ['email_address' => $emailAddress];
		if ($row = $db->get_row($query, $params)) {
			$this->mapRowToProperties($row);
			return true;
		}
		$this->emailAddress = $emailAddress;
		return false;
	}


	public static function validateOTP($email, $otp): bool
	{
		$db = Database::getInstance();
		$query = "
			SELECT * FROM users 
			WHERE 
				email_address = :email_address 
				AND reset_token = :otp
				AND NOW() < token_expiration
		";
		$params = [
			'email_address' => $email,
			'otp' => $otp
		];
		if ($db->get_row($query, $params)) return true;
		return false;
	}


	public static function login(string $username, string $password): mixed
	{
		$db = Database::getInstance();
		$query = "SELECT * FROM users WHERE username = :username AND password = :password";
		$params = [
  		'username' => $username,
  		'password' => md5($password)
		];
		return $db->get_row($query, $params);		
	}

	// TODO: This makes no sense here! Change it!
	public static function getUserSession(string $username): mixed
	{
		$db = Database::getInstance();
		$query = "SELECT * FROM users WHERE username = :username";
		$params = ['username' => $username];
		$result = $db->get_row($query, $params);
		$_SESSION['user'] = $result;
		return $result;
	}


	public function hasRole(array $roles): bool
	{
		foreach ($roles as $role) {
			if (array_search($role, $this->roles) !== false) {
				return true;
			}
		}
		return false;
	}


	public function addRole(string $role): void
	{
		$this->roles[] = $role;
	}

	public function getPreferences(): ?object
	{
		return json_decode($this->preferences);
	}
}
