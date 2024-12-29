<?php
require_once 'class.data.php';
if (!isset($db)) $db = new data();


class Guest
{
	private $row;

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
			$this->row = $row;

			$this->guestId = $row->id;
			$this->firstName = $row->first_name;
			$this->lastName = $row->last_name;
			$this->emailAddress = $row->email_address;
			$this->phoneNumber = $row->phone_number;
			return true;
		}
		return false;
	}

	static public function getGuestByPhoneNumber($phoneNumber)
	{
		global $db;
		$sql = 'SELECT * FROM guests WHERE phone_number = :phone_number';
		$data = ['phone_number' => $phoneNumber];
		if ($row = $db->get_row($sql, $data)) {
			$guest = new Guest(null);
			$guest->row = $row;

			$guest->guestId = $row->id;
			$guest->firstName = $row->first_name;
			$guest->lastName = $row->last_name;
			$guest->emailAddress = $row->email_address;
			$guest->phoneNumber = $row->phone_number;
			return $guest;
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
		
		if ($this->guestId) {
			$this->getGuest($this->guestId);
		} else {
			$this->getGuest($result);
		}

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

	public function getState(): string
	{
		return json_encode($this->row);
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
