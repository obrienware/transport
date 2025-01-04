<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.audit.php';
require_once 'class.data.php';

class User
{
	private $db;
	private $id;
	private $row;
	private $lastError;
	private $action = 'create';
	private $archived;

	public $username;
	public $firstName;
	public $lastName;
	public $emailAddress;
	public $phoneNumber;
	public $roles;
	public $position;
	public $departmentId;
	public $CDL;
	public $changePassword;
	public $preferences;

	public $otp;


	public function __construct(int $id = null)
	{
		$this->db = new data();
		if (isset($id)) $this->load($id);
	}


	public function load(int $id): bool
	{
		$query = 'SELECT * FROM users WHERE id = :id';
		$params = ['id' => $id];
		if ($row = $this->db->get_row($query, $params)) {
			$this->row = $row;
			$this->action = 'update';

			$this->id = $row->id;
			$this->username = $row->username;
			$this->firstName = $row->first_name;
			$this->lastName = $row->last_name;
			$this->emailAddress = $row->email_address;
			$this->phoneNumber = $row->phone_number;
			$this->roles = $row->roles ? explode(',', $row->roles) : null;
			$this->position = $row->position;
			$this->departmentId = $row->department_id;
			$this->CDL = $row->cdl;
			$this->changePassword = $row->change_password;
			$this->preferences = ($row->personal_preferences) ? json_decode($row->personal_preferences) : null;
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
		$audit->table = 'users';
		$audit->before = json_encode($this->row);

		$params = [
			'username' => $this->username,
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'email_address' => $this->emailAddress,
			'phone_number' => $this->phoneNumber,
			'roles' => $this->roles ? implode(',', $this->roles) : null,
			'position' => $this->position,
			'department_id' => $this->departmentId,
			'cdl' => $this->CDL ? 1 : 0,
			'personal_preferences' => ($this->preferences) ? json_encode($this->preferences) : NULL,
			'user' => $userResponsibleForOperation
		];

		if ($this->action === 'update') {
			$audit->description = 'User updated: '.$this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE users SET
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
			$audit->description = 'User created: '.$this->getName();
			$query = "
				INSERT INTO users SET
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
		$audit->table = 'users';
		$audit->before = json_encode($this->row);

		$query = "
			UPDATE users 
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
			$audit->description = 'User deleted: '.$this->getName();
			$audit->commit();
			$this->reset();
			return true;	
		} catch (Exception $e) {
			$this->lastError = $e->getMessage();
			return false;
		}
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

		$this->username = null;
		$this->firstName = null;
		$this->lastName = null;
		$this->emailAddress = null;
		$this->phoneNumber = null;
		$this->roles = null;
		$this->position = null;
		$this->departmentId = null;
		$this->CDL = null;
		$this->changePassword = null;
		$this->preferences = null;

		// Reinitialize the database connection if needed
		$this->db = new data();
	}


	public function getLastError(): string | null
	{
		return $this->lastError;
	}


	public function getUsername(): string
	{
		return $this->username ? $this->username : $this->emailAddress;
	}


	public function resetPassword(): bool
	{
		if ($this->emailAddress) {
			require_once 'class.utils.php';
			require_once 'class.email.php';
			$newPassword = Utils::randomPassword(10);
			$query = 'UPDATE users SET password = :password, change_password = 1 WHERE id = :user_id';
			$params = ['password' => md5($newPassword), 'user_id' => $this->id];
			$this->db->query($query, $params);

			$email = new Email();
			$email->setSubject('Your password has been reset.');
			$email->setContent("Your new temporary password has been set to\n\n{$newPassword}\n\nVisit https://{$_SERVER['HTTP_HOST']}");
			$email->addRecipient($this->emailAddress, $this->getName());
			$email->sendText();
			return true;
		}
		return false;
	}


	static public function sendResetLink(string $username) 
	{
		// First determine if we have a user that matches the given username
		$db = new data();
		require_once 'class.email.php';
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
		$query = "
			UPDATE users SET 
				reset_token = :token, token_expiration = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
			WHERE id = :user_id
		";
		$params = [
			'token' => bin2hex(random_bytes(10 / 2)),
			'user_id' => $this->id
		];
		$this->db->query($query, $params);
		return $params['token'];
	}


	public function setNewPassword($newPassword)
	{
		$query = 'UPDATE users SET password = :new_password, change_password = 0, reset_token = NULL, token_expiration = NULL WHERE id = :id';
		$params = ['new_password' => md5($newPassword), 'id' => $this->id];
		return $this->db->query($query, $params);
	}


	static public function getDrivers(): array
	{
		return self::getUsersByRole('driver');
	}


	static public function getManagers(): array
	{
		return self::getUsersByRole('manager');
	}


	static public function getUsersByRole(string $role): array
	{
		$db = new data();
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


	static public function getUsers(): array
	{
		$db = new data();
		$query = "
			SELECT u.*, d.name AS department FROM users u 
			LEFT OUTER JOIN departments d ON d.id = u.department_id
			WHERE u.archived IS NULL ORDER BY first_name, last_name
		";
		return $db->get_rows($query);
	}


	public function getUserByEmail($emailAddress): bool
	{
		$query = "SELECT * FROM users WHERE email_address = :email_address AND archived IS NULL";
		$params = ['email_address' => $emailAddress];
		if ($row = $this->db->get_row($query, $params)) {
			$this->load($row->id);
			return true;
		}
		$this->emailAddress = $emailAddress;
		return false;
	}


	static public function validateOTP($email, $otp)
	{
		$db = new data();
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


	static public function login(string $username, string $password): mixed
	{
		$db = new data();
		$query = "SELECT * FROM users WHERE username = :username AND password = :password";
		$params = [
  		'username' => $username,
  		'password' => md5($password)
		];
		return $db->get_row($query, $params);		
	}

	// TODO: This makes no sense here! Change it!
	static public function getUserSession(string $username): mixed
	{
		$db = new data();
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
		if ($this->roles) {
			$this->roles[] = $role;
		} else {
			$this->roles = [$role];
		}
	}


	public function getName(): string
	{
		if ($this->id) return "{$this->firstName} {$this->lastName}";
		return '';
	}
}
