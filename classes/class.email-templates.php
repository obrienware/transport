<?php
require_once 'class.audit.php';
require_once 'class.data.php';

class EmailTemplates
{
  private $id;
  private $db;
  private $row;
	private $lastError;
	private $action = 'create';

  public $name;
  public $content;
  public $availableVariables = [];
  
  
  public function __construct(int $id = null) {
    $this->db = new data();
    if (isset($id)) $this->load($id);
  }

  
  public function load(int $id): bool
  {
    $query = "SELECT * FROM email_templates WHERE id = :id";
    $params = ['id' => $id];
    if ($row = $this->db->get_row($query, $params)) {
      $this->row = $row;
      $this->action = 'update';

      $this->id = $row->id;
      $this->name = $row->name;
      $this->content = $row->content;
      $this->availableVariables = explode(', ', $row->available_variables);
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
		$audit->table = 'email_templates';
		$audit->before = json_encode($this->row);

		$params = [
      'content' => $this->content,
      'user' => $userResponsibleForOperation
		];

    if ($this->action === 'update') {
			$audit->description = 'Email Template updated: '.$this->name;
			$params['id'] = $this->id;
			$query = "
				UPDATE email_templates SET
					content = :content,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		}
    // In this case the user won't be able to create a new email template

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


  // User also won't be able to delete an email template


  static public function getAll(): array
	{
		$db = new data();
		$query = 'SELECT * FROM email_templates WHERE archived IS NULL ORDER BY name';
		return $db->get_rows($query);
	}
  
  
  public function getLastError(): string | null
  {
    return $this->lastError;
  }
  
  
  public static function get($templateName) {
    $db = new data();
    $sqlQuery = "SELECT content FROM email_templates WHERE name = :name";
    $params = ['name' => $templateName];
    return $db->get_var($sqlQuery, $params);
  }
}