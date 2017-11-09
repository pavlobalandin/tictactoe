<?php

namespace AppBundle\Model;

abstract class AbstractModel
{
	const KEY_CREATED = 'created';
	const KEY_UPDATED = 'updated';

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
	 * @param string $filePath
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

	/**
	 * @return bool
	 */
	public function save()
	{
		$this->updated = new \DateTime();
		$fp = fopen($this->path, 'wb');
		$res = fputs($fp, $this->toString());
		fclose($fp);
		return (bool) $res;
	}

	/**
	 * @return bool
	 */
	public function delete()
	{
		return unlink($this->path);
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
			'id'              => $this->id,
			self::KEY_CREATED => $this->created->format('c'),
			self::KEY_UPDATED => $this->updated->format('c'),
			'path'            => $this->path,
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
			if (in_array($key, [self::KEY_CREATED, self::KEY_UPDATED])) {
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