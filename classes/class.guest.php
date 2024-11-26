<?php

require_once 'class.data.php';
if (!isset($db)) {
	$db = new data();
}


class Guest
{
	public $guestId;
	public $firstName;
	public $lastName;
	public $emailAddress;
	public $phoneNumber;

	public function __construct($guestId)
	{
		if (isset($guestId)) {
			$this->getGuest($guestId);
		}
	}

	public function getGuest($guestId)
	{
		global $db;
		$sql = 'SELECT * FROM guests WHERE id = :guest_id';
		$data = ['guest_id' => $guestId];
		if ($row = $db->get_row($sql, $data)) {
			$this->guestId = $row->id;
			$this->firstName = $row->first_name;
			$this->lastName = $row->last_name;
			$this->emailAddress = $row->email_address;
			$this->phoneNumber = $row->phone_number;
			return true;
		}
		return false;
	}

	public function getName(): string
	{
		return "{$this->firstName} {$this->lastName}";
	}

	static function getGuests()
	{
		global $db;
		$sql = "SELECT * FROM guests WHERE archived IS NULL ORDER BY first_name, last_name";
		return $db->get_results($sql);
	}

	public function save()
	{
		global $db;
		$data = [
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'email_address' => $this->emailAddress,
			'phone_number' => $this->phoneNumber
		];
		if ($this->guestId) {
			// Update
			$data['id'] = $this->guestId;
			$sql = "
				UPDATE guests SET
					first_name = :first_name,
					last_name = :last_name,
					email_address = :email_address,
					phone_number = :phone_number
				WHERE id = :id
			";
		} else {
			// Insert
			$sql = "
				INSERT INTO guests SET
					first_name = :first_name,
					last_name = :last_name,
					email_address = :email_address,
					phone_number = :phone_number,
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

	static function deleteGuest($guestId)
	{
		global $db;
		$sql = 'UPDATE guests SET archived = NOW(), archived_by = :user WHERE id = :guest_id';
		$data = ['user' => $_SESSION['user']->username, 'guest_id' => $guestId];
		return $db->query($sql, $data);
	}

	public function delete()
	{
		return $this->deleteGuest($this->guestId);
	}

}
