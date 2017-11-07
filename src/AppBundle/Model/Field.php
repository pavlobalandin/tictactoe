<?php
namespace AppBundle\Model;

class Field
{
	const WIDTH = 3;
	const HEIGHT = 3;

	/** @var array */
	private $current;

	/**
	 * @return array
	 */
	public function getCurrent()
	{
		return $this->current;
	}

	/**
	 * @param array $current
	 */
	public function setCurrent(array $current)
	{
		$this->current = $current;
	}

	public function cleanup()
	{
		for ($h = 0; $h < self::WIDTH; $h++) {
			for ($w = 0; $w < self::HEIGHT; $w++) {
				$this->current[$h][$w] = NULL;
			}
		}
	}
}