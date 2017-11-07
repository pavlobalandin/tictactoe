<?php
namespace AppBundle\Model;

abstract class AbstractModel
{
	/** @var string */
	private $id;

	/** @var \DateTime */
	private $created;

	/** @var \DateTime */
	private $updated;

	/** @var string */
	private $path;

	/**
	 * Game constructor.
	 * @param $filePath
	 */
	public function __construct($filePath = NULL)
	{
		if ($filePath === NULL) {
			return;
		}

		$this->path = $filePath;

		if (!file_exists($this->path)) {
			$this->id = basename($this->path);
			$this->created = new \DateTime();
			$this->save();
		} else {
			$this->loadFromString(file_get_contents($filePath));
		}
	}

	public function save()
	{
		$this->updated = new \DateTime();
		$fp = fopen($this->path, 'wb');
		fputs($fp, $this->toString());
		fclose($fp);
	}

	public function delete()
	{
		unlink($this->path);
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @return string
	 */
	private function toString()
	{
		$payload = [
			'id'      => $this->id,
			'created' => $this->created->format('c'),
			'updated' => $this->updated->format('c'),
			'path'    => $this->path,
		];

		foreach ($this->getCustomPayload() as $key => $value) {
			$payload[$key] = $value;
		}

		return json_encode($payload);
	}

	/**
	 * @param string $payload
	 */
	private function loadFromString($payload)
	{
		$payload = json_decode($payload, TRUE);
		foreach ($payload as $key => $value) {
			if (in_array($key, ['created', 'updated'])) {
				$this->$key = new \DateTime($value);
				continue;
			}
			$this->$key = $this->loadKey($key, $value);
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	protected function loadKey($key, $value)
	{
		return $value;
	}

	abstract function getCustomPayload();
}