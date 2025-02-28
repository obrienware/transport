<?php

declare(strict_types=1);

namespace Transport;

require_once __DIR__ . '/../../autoload.php';

use DateTime;
use DateTimeZone;

class Location extends Base
{
	protected $tableName = 'locations';
	protected $tableDescription = 'Locations';

	public ?string $name = null;
	public ?string $shortName = null;
	public ?string $mapAddress = null;
	public ?string $description = null;
	public ?float $lat = null;
	public ?float $lon = null;
	public ?string $placeId = null; // For backward compatibility
	public ?string $osmType = null;
	public ?string $osmId = null;
	public ?string $type = null;
	public ?string $IATA = null;
	public ?string $meta = null;

	public function getName(): string
	{
		return is_null($this->name) ? 'no-name' : $this->name;
	}

	public function load(int $id): bool
	{
		$db = Database::getInstance();
		$query = "SELECT * FROM {$this->tableName} WHERE id = :id";
		$params = ['id' => $id];
		if ($row = $db->get_row($query, $params))
		{
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
		$this->name = $row->name;
		$this->shortName = $row->short_name;
		$this->mapAddress = $row->map_address;
		$this->description = $row->description;
		$this->lat = is_null($row->lat) ? null : (float) $row->lat;
		$this->lon = is_null($row->lon) ? null : (float) $row->lon;
		$this->placeId = $row->place_id; // For backward compatibility
		$this->osmType = $row->osm_type;
		$this->osmId = $row->osm_id;
		$this->type = $row->type;
		$this->IATA = $row->iata;
		$this->meta = $row->meta;

		if (!empty($row->archived))
		{
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
			'name' => $this->name,
			'short_name' => $this->shortName,
			'description' => $this->description,
			'type' => $this->type,
			'iata' => $this->IATA,
			'map_address' => $this->mapAddress,
			'lat' => $this->lat,
			'lon' => $this->lon,
			'place_id' => $this->placeId, // For backward compatibility
			'osm_type' => $this->osmType,
			'osm_id' => $this->osmId,
			'user' => $userResponsibleForOperation,
		];

		if ($this->action === 'update')
		{
			$audit->description = $this->tableDescription . ' updated: ' . $this->getName();
			$params['id'] = $this->id;
			$query = "
				UPDATE {$this->tableName} SET
					name = :name,
					short_name = :short_name,
					description = :description,
					type = :type,
					iata = :iata,
					map_address = :map_address,
					lat = :lat,
					lon = :lon,
					place_id = :place_id,
					osm_type = :osm_type,
					osm_id = :osm_id,
					modified = NOW(),
					modified_by = :user
				WHERE id = :id
			";
		}
		else
		{
			$audit->description = $this->tableDescription . ' created: ' . $this->getName();
			$query = "
				INSERT INTO {$this->tableName} SET
					name = :name,
					short_name = :short_name,
					description = :description,
					type = :type,
					iata = :iata,
					map_address = :map_address,
					lat = :lat,
					lon = :lon,
					place_id = :place_id,
					osm_type = :osm_type,
					osm_id = :osm_id,
					created = NOW(),
					created_by = :user
			";
		}
		try
		{
			$result = $db->query($query, $params);
			$id = ($this->action === 'create') ? $result : $this->id;
			$this->load($id);
			$audit->after = json_encode($this->row);
			$audit->commit();
			return true;
		}
		catch (\Exception $e)
		{
			$this->lastError = $e->getMessage();
			return false;
		}
	}

	public static function getAll(): array
	{
		$db = Database::getInstance();
		$query = "SELECT * FROM locations WHERE archived IS NULL ORDER BY name";
		return $db->get_rows($query);
	}

	protected function reset(): void
	{
		parent::reset();

		$this->name = null;
		$this->shortName = null;
		$this->mapAddress = null;
		$this->description = null;
		$this->lat = null;
		$this->lon = null;
		$this->placeId = null; // For backward compatibility
		$this->osmType = null;
		$this->osmId = null;
		$this->type = null;
		$this->IATA = null;
		$this->meta = null;
	}
}
