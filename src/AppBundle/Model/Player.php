<?php
namespace AppBundle\Model;

class Player
{
	private $id;

	public function __construct($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function register(Game $game)
	{
		if (!$game->hasPlayer($this->id)) {
			$game->addPlayer($this->id);
			$game->save();
		}
	}
}