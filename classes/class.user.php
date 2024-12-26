<?php
require_once 'class.data.php';

class User
{
	private $db;
	private $row;

	public $userId;
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

	public function __construct(int $userId = null)
	{
		$this->db = new data();
		if (isset($userId)) {
			$this->getUser($userId);
		}
	}

	public function getUser(int $userId): bool
	{
		$sql = 'SELECT * FROM users WHERE id = :user_id';
		$data = ['user_id' => $userId];
		if ($row = $this->db->get_row($sql, $data)) {
			$this->row = $row;

			$this->userId = $row->id;
			$this->username = $row->username;
			$this->firstName = $row->first_name;
			$this->lastName = $row->last_name;
			$this->emailAddress = $row->email_address;
			$this->phoneNumber = $row->phone_number;
			$this->roles = explode(',', $row->roles);
			$this->position = $row->position;
			$this->departmentId = $row->department_id;
			$this->CDL = $row->cdl;
			$this->changePassword = $row->change_password;
			$this->preferences = ($row->personal_preferences) ? json_decode($row->personal_preferences) : null;
			return true;
		}
		return false;
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

	public function getName(): string
	{
		if ($this->userId) return "{$this->firstName} {$this->lastName}";
		return '';
	}

	public function save(): array
	{
		$data = [
			'username' => $this->username,
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'email_address' => $this->emailAddress,
			'phone_number' => $this->phoneNumber,
			'roles' => $this->roles ? implode(',', $this->roles) : null,
			'position' => $this->position,
			'department_id' => $this->departmentId,
			'cdl' => $this->CDL ? 1 : 0,
			'personal_preferences' => ($this->preferences) ? json_encode($this->preferences) : NULL
		];
		if ($this->userId) {
			// Update
			$data['id'] = $this->userId;
			$sql = "
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
					personal_preferences = :personal_preferences
				WHERE id = :id
			";
		} else {
			// Insert
			$sql = "
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
			$data['user'] = $_SESSION['user']->username ?: 'system';
		}
		$result = $this->db->query($sql, $data);
		if (!$this->userId) $this->getUser($result); // If we are adding a user, then lets get a clean instance of the user
		return [
			'result' => $result,
			'errors' => $this->db->errorInfo
		];
	}

	public function resetPassword(): bool
	{
		if ($this->emailAddress) {
			require_once 'class.utils.php';
			require_once 'class.email.php';
			$newPassword = Utils::randomPassword(10);
			$sql = 'UPDATE users SET password = :password, change_password = 1 WHERE id = :user_id';
			$data = ['password' => md5($newPassword), 'user_id' => $this->userId];
			$this->db->query($sql, $data);

			$email = new Email();
			$email->setSubject('Your password has been reset.');
			$email->setContent("Your new temporary password has been set to\n\n{$newPassword}\n\nVisit https://{$_SERVER['HTTP_HOST']}");
			$email->addRecipient($this->emailAddress, $this->firstName);
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
		$sql = "SELECT id FROM users WHERE username = :username AND archived IS NULL";
		$data = ['username' => $username];
		$id = $db->get_var($sql, $data);
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
		$sql = "
			UPDATE users SET 
				reset_token = :token, token_expiration = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
			WHERE id = :user_id
		";
		$data = [
			'token' => bin2hex(random_bytes(10 / 2)),
			'user_id' => $this->userId
		];
		$this->db->query($sql, $data);
		return $data['token'];
	}

	public function setNewPassword($newPassword)
	{
		$sql = 'UPDATE users SET password = :new_password, change_password = 0, reset_token = NULL, token_expiration = NULL WHERE id = :id';
		$data = ['new_password' => md5($newPassword), 'id' => $this->userId];
		return $this->db->query($sql, $data);
	}

	static public function deleteUser($userId)
	{
		$db = new data();
		$sql = 'UPDATE users SET archived = NOW(), archived_by = :user WHERE id = :user_id';
		$data = ['user' => $_SESSION['user']->username, 'user_id' => $userId];
		return $db->query($sql, $data);
	}

	public function delete()
	{
		return $this->deleteUser($this->userId);
	}

	/**
	 * Caution: This is used internally by unit testing to cleanly remove data. 
	 * Use the delete method instead in production to archive the user instead of completely removing the data
	 */
	public function remove()
	{
		$result = $this->db->query(
			"DELETE FROM users WHERE id = :user_id",
			['user_id' => $this->userId]
		);
		// Reset the object (as best we can)
		foreach (get_class_vars(get_class($this)) as $name => $default) 
  		$this->$name = $default;
		unset($this->db);
		unset($this->row);
		return $result;
	}

	static public function getDrivers()
	{
		$db = new data();
		$sql = "
			SELECT * FROM users 
			WHERE 
				FIND_IN_SET('driver', roles)
				AND archived IS NULL
			ORDER BY first_name, last_name
		";
		return $db->get_results($sql);
	}

	static public function getUsers()
	{
		$db = new data();
		$sql = "
			SELECT u.*, d.name AS department FROM users u 
			LEFT OUTER JOIN departments d ON d.id = u.department_id
			WHERE u.archived IS NULL ORDER BY first_name, last_name
		";
		return $db->get_results($sql);
	}

	public function getUserByEmail($emailAddress)
	{
		$sql = "SELECT * FROM users WHERE email_address = :email_address AND archived IS NULL";
		$data = ['email_address' => $emailAddress];
		if ($row = $this->db->get_row($sql, $data)) {
			$this->getUser($row->id);
			return true;
		}
		$this->emailAddress = $emailAddress;
		return false;
	}

	static public function validateOTP($email, $otp)
	{
		$db = new data();
		$sql = "
			SELECT * FROM users 
			WHERE 
				email_address = :email_address 
				AND reset_token = :otp
				AND NOW() < token_expiration
		";
		$data = [
			'email_address' => $email,
			'otp' => $otp
		];
		if ($item = $db->get_row($sql, $data)) return true;
		return false;
	}

	public function getState(): string
	{
		return json_encode($this->row);
	}

	static public function login(string $username, string $password): mixed
	{
		$db = new data();
		$sql = "SELECT * FROM users WHERE username = :username AND password = :password";
		$data = [
  		'username' => $username,
  		'password' => md5($password)
		];
		return $db->get_row($sql, $data);		
	}

	static public function getUserSession(string $username): mixed
	{
		$db = new data();
		$sql = "SELECT * FROM users WHERE username = :username";
		$data = ['username' => $username];
		$result = $db->get_row($sql, $data);
		$_SESSION['user'] = $result;
		return $result;
	}

	static public function formattedPhoneNumber(string $number): string
	{
		if (str_contains($number, '+')) {
			return $number;
		} else {
			return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number);
		}	
	}

}
