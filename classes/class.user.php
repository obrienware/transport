<?php
require_once 'class.data.php';
if (!isset($db)) {
	$db = new data();
}

class User
{
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

	public function __construct(int $userId = null)
	{
		if (isset($userId)) {
			$this->getUser($userId);
		}
	}

	public function getUser(int $userId): bool
	{
		global $db;
		$sql = 'SELECT * FROM users WHERE id = :user_id';
		$data = ['user_id' => $userId];
		if ($row = $db->get_row($sql, $data)) {
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
		return "{$this->firstName} {$this->lastName}";
	}

	public function save(): array
	{
		global $db;
		$data = [
			'username' => $this->username,
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'email_address' => $this->emailAddress,
			'phone_number' => $this->phoneNumber,
			'roles' => implode(',', $this->roles),
			'position' => $this->position,
			'department_id' => $this->departmentId,
			'cdl' => $this->CDL ? 1 : 0
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
					cdl = :cdl
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
					created = NOW(),
					created_by = :user
			";
			$data['user'] = $_SESSION['user']->username;
		}
		$result = $db->query($sql, $data);
		return [
			'result' => $result,
			'errors' => $db->errorInfo
		];
	}

	public function resetPassword(): bool
	{
		if ($this->emailAddress) {
			global $db;
			require_once 'class.utils.php';
			require_once 'class.email.php';
			$newPassword = Utils::randomPassword(10);
			$sql = 'UPDATE users SET password = :password, change_password = 1 WHERE id = :user_id';
			$data = ['password' => md5($newPassword), 'user_id' => $this->userId];
			$db->query($sql, $data);

			$email = new Email();
			$email->setSubject('Your password has been reset.');
			$email->setContent("Your new temporary password has been set to\n\n{$newPassword}\n\nVisit http://{$_SERVER['HTTP_HOST']}");
			$email->addRecipient($this->emailAddress, $this->firstName);
			$email->sendText();
			return true;
		}
		return false;
	}

	static public function sendResetLink(string $username) 
	{
		// First determine if we have a user that matches the given username
		global $db;
		require_once 'class.email.php';
		$sql = "SELECT id FROM users WHERE username = :username AND archived IS NULL";
		$data = ['username' => $username];
		$id = $db->get_var($sql, $data);
		if ($id) {
			$tmpUser = new User($id);
			$token = $tmpUser->setPasswordToken();

			$email = new Email();
			$email->setSubject('Password reset.');
			$email->setContent("To reset your password, please navigate to the following link : http://{$_SERVER['HTTP_HOST']}/reset/{$token}");
			$email->addRecipient($tmpUser->emailAddress, $tmpUser->firstName);
			return $email->sendText();
		}
		return false;
	}

	private function setPasswordToken(): string
	{
		global $db;
		$sql = "
			UPDATE users SET 
				reset_token = :token, token_expiration = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
			WHERE id = :user_id
		";
		$data = [
			'token' => bin2hex(random_bytes(10 / 2)),
			'user_id' => $this->userId
		];
		$db->query($sql, $data);
		return $data['token'];
	}

	public function setNewPassword($newPassword)
	{
		global $db;
		$sql = 'UPDATE users SET password = :new_password, change_password = 0 WHERE id = :id';
		$data = ['new_password' => md5($newPassword), 'id' => $this->userId];
		return $db->query($sql, $data);
	}

	static public function deleteUser($userId)
	{
		global $db;
		$sql = 'UPDATE users SET archived = NOW(), archived_by = :user WHERE id = :user_id';
		$data = ['user' => $_SESSION['user']->username, 'user_id' => $userId];
		return $db->query($sql, $data);
	}

	public function delete()
	{
		return $this->deleteUser($this->userId);
	}

	static public function getDrivers()
	{
		global $db;
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
		global $db;
		$sql = "
			SELECT u.*, d.name AS department FROM users u 
			LEFT OUTER JOIN departments d ON d.id = u.department_id
			WHERE u.archived IS NULL ORDER BY first_name, last_name
		";
		return $db->get_results($sql);
	}
}