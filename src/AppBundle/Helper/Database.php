<?php
namespace AppBundle\Helper;

class Database
{
	/** @var string */
	private $instancePrefix;

	/** @var string */
	private $instanceName;

	/** @var string */
	private $dbPath;

	public function __construct($instance, $dbPath)
	{
		$classPath = explode('\\', get_class($instance));
		$this->instancePrefix = strtolower(end($classPath));
		$this->instanceName = get_class($instance);
		$this->dbPath = $dbPath;
	}

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

	public function insert() {
		$itemId = String::uniqid();
		$fullPath = $this->dbPath . DIRECTORY_SEPARATOR . $this->instancePrefix . '_' . $itemId;
		$game = new $this->instanceName($fullPath);
		return $game;
	}

	public function update($instance)
	{
		$instance->save();
	}
}