<?php

namespace AppBundle\Helper;

use AppBundle\Model\AbstractModel;

class Database
{
	/** @var string */
	private $instancePrefix;

	/** @var string */
	private $instanceName;

	/** @var string */
	private $dbPath;

	/**
	 * Database constructor.
	 * @param AbstractModel $instance
	 * @param string $dbPath
	 */
	public function __construct(AbstractModel $instance, $dbPath)
	{
		$classPath = explode('\\', get_class($instance));
		$this->instancePrefix = strtolower(end($classPath));
		$this->instanceName = get_class($instance);
		$this->dbPath = $dbPath;
	}

	/**
	 * @return \Generator
	 */
	public function fetchAll()
	{
		$dir = opendir($this->dbPath);
		while ($file = readdir($dir)) {
			if (preg_match('/^' . $this->instancePrefix . '/', $file)) {
				$fullPath = $this->dbPath . DIRECTORY_SEPARATOR . $file;
				yield new $this->instanceName($fullPath);
			}
		}
		closedir($dir);
	}

	/**
	 * @return AbstractModel
	 */
	public function insert()
	{
		$itemId = uniqid();
		$fullPath = $this->dbPath . DIRECTORY_SEPARATOR . $this->instancePrefix . '_' . $itemId;
		$game = new $this->instanceName($fullPath);
		return $game;
	}

	/**
	 * @param AbstractModel $instance
	 * @return bool
	 */
	public function update(AbstractModel $instance)
	{
		return $instance->save();
	}
}