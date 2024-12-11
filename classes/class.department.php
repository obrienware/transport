<?php
require_once 'class.data.php';
if (!isset($db))  $db = new data();

class Department
{
	private $row;
  private $departmentId;
  public $name;
  public $mayRequest;

  public function __construct(int $departmentId = null)
  {
    if ($departmentId) {
      $this->getDepartment($departmentId);
    }
  }

  public function getDepartment(int $departmentId): bool
  {
    global $db;
    $sql = "SELECT * FROM departments WHERE id = :department_id";
    $data = ['department_id' => $departmentId];
    if ($item = $db->get_row($sql, $data)) {
			$this->row = $item;

      $this->departmentId = $item->id;
      $this->name = $item->name;
      $this->mayRequest = $item->can_submit_requests;
      return true;
    }
    return false;
  }

  public function getDepartmentId()
  {
    return $this->departmentId;
  }

  static public function getDepartments()
  {
    global $db;
    $sql = "SELECT * FROM departments WHERE archived IS NULL ORDER BY name";
    return $db->get_results($sql);
  }

  public function save()
  {
		global $db;
		$data = [
			'name' => $this->name,
			'can_submit_requests' => $this->mayRequest
		];
		if ($this->departmentId) {
			// Update
			$data['id'] = $this->departmentId;
			$sql = "
				UPDATE departments SET
					name = :name,
					can_submit_requests = :can_submit_requests
				WHERE id = :id
			";
		} else {
			// Insert
			$sql = "
				INSERT INTO departments SET
					name = :name,
					can_submit_requests = :can_submit_requests,
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

	static function deleteDepartment($departmentId)
	{
		global $db;
		$sql = 'UPDATE departments SET archived = NOW(), archived_by = :user WHERE id = :department_id';
		$data = ['user' => $_SESSION['user']->username, 'department_id' => $departmentId];
		return $db->query($sql, $data);
	}

	public function delete()
	{
		return $this->deleteDepartment($this->departmentId);
	}

	public function getState()
	{
		return json_encode($this->row);
	}

}