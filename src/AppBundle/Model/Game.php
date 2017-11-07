<?php
namespace AppBundle\Model;

use AppBundle\Helper\String;

class Game extends AbstractModel
{
	const MAX_PLAYERS_COUNT = 2;

	const KEY_FIELD   = 'field';
	const KEY_PLAYERS = 'players';
	const KEY_WAITING = 'waiting';

	const LABEL_OTHER_PARTY = 'Other party';
	const LABEL_YOU         = 'You';

	const SYM_FIRST  = 'X';
	const SYM_SECOND = 'O';

	/** @var array */
	protected $players;

	/** @var string */
	protected $waiting;

	/** @var Field */
	protected $field;

	/** @var array */
	protected $freeSymbols;

	public function __construct($filePath = NULL)
	{
		$this->players = [];
		$this->freeSymbols = [self::SYM_FIRST, self::SYM_SECOND];
		$this->field = new Field();
		$this->field->cleanup();
		parent::__construct($filePath);

		foreach ($this->freeSymbols as $id => $symbol) {
			if (array_key_exists($symbol, $this->players)) {
				unset($this->freeSymbols[$id]);
			}
		}

		if ($this->waiting === NULL && count($this->freeSymbols) == 0) {
			$this->waiting = $this->players[self::SYM_FIRST];
			$this->save();
		}
	}

	/**
	 * @param string $playerId
	 * @throws \Exception
	 */
	public function addPlayer($playerId)
	{
		if (strlen($playerId) < String::UNIQUE_ID_SIZE) {
			throw new \Exception('Invalid ID size: ' . $playerId . '.');
		}
		if (count($this->players) >= self::MAX_PLAYERS_COUNT) {
			throw new \Exception('Maximum number of players reached.');
		}

		$this->freeSymbols = array_values($this->freeSymbols);

		$symbolId = rand(0, count($this->freeSymbols) - 1);
		$this->players[$this->freeSymbols[$symbolId]] = $playerId;

		unset($this->freeSymbols[$symbolId]);
	}

	/**
	 * @param string $playerId
	 * @return bool
	 */
	public function hasPlayer($playerId)
	{
		return in_array($playerId, $this->players);
	}

	/**
	 * @return array
	 */
	public function getPlayers()
	{
		return $this->players;
	}

	/**
	 * @return int
	 */
	public function getPlayersCount()
	{
		return count($this->players);
	}

	/**
	 * @return Field
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @return mixed
	 */
	public function getWaiting()
	{
		return $this->waiting;
	}

	public function setField(Field $field)
	{
		$this->field = $field;
	}

	public function changeWaiting()
	{
		$currentPosition = NULL;
		$tail = [];
		$nextFound = FALSE;
		foreach ($this->players as $symbol => $playerId) {
			if (empty($this->waiting)) {
				$currentPosition = $playerId;
				break;
			}

			if ($this->waiting === $playerId) {
				$nextFound = TRUE;
				continue;
			}
			if (!$nextFound) {
				$tail[] = $playerId;
			} else {
				$currentPosition = $playerId;
				break;
			}
		}
		if ($currentPosition === NULL && count($tail)) {
			$currentPosition = array_shift($tail);
		}

		$this->waiting = $currentPosition;
	}

	/**
	 * @param string $playerId
	 * @return string
	 * @throws \Exception
	 */
	public function getPlayerSymbol($playerId)
	{
		$players = array_flip($this->players);
		if (!array_key_exists($playerId, $players)) {
			throw new \Exception('Player not exists in this game.');
		}
		return $players[$playerId];
	}

	/**
	 * @return array
	 */
	public function getCustomPayload()
	{
		return [
			self::KEY_PLAYERS => $this->players,
			self::KEY_FIELD   => $this->field->getCurrent(),
			self::KEY_WAITING => $this->waiting,
		];
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	protected function loadKey($key, $value)
	{
		switch($key) {
			case self::KEY_FIELD:
				$item = new Field();
				$item->setCurrent($value);
				return $item;
			default:
				break;
		}
		return parent::loadKey($key, $value);
	}

	public function getWinner()
	{
		$current = $this->field->getCurrent();
		$winner = NULL;

		// Horizontal layer
		for ($h = 0; $h < Field::HEIGHT; $h++) {
			$begin = $current[$h][0];
			$itemsInLine = 0;
			if ($begin === NULL) {
				continue;
			}
			for ($w = 0; $w < Field::WIDTH; $w++) {
				if ($current[$h][$w] === NULL || $current[$h][$w] != $begin) {
					break;
				}
				$itemsInLine++;
			}
			if ($itemsInLine == Field::WIDTH) {
				$winner = $begin;
				break;
			}
		}

		// Vertical layer
		for ($w = 0; $w < Field::WIDTH; $w++) {
			$begin = $current[0][$w];
			$itemsInLine = 0;
			if ($begin === NULL) {
				continue;
			}
			for ($h = 0; $h < Field::HEIGHT; $h++) {
				if ($current[$h][$w] === NULL || $current[$h][$w] != $begin) {
					break;
				}
				$itemsInLine++;
			}
			if ($itemsInLine == Field::HEIGHT) {
				$winner = $begin;
				break;
			}
		}

		// Diagonal
		foreach ([0, Field::HEIGHT - 1] as $start) {
			$begin = $current[$start][0];
			if ($begin === NULL) {
				continue;
			}
			$itemsInLine = 0;
			$w = 0;
			for ($h = $start; ($start == 0 ? $h < Field::HEIGHT: $h >= 0); ($start == 0 ? $h++ : $h--)) {
				if (!isset($current[$h][$w]) || $current[$h][$w] != $begin) {
					break;
				}
				$itemsInLine++;
				$w++;
			}
			if ($itemsInLine == Field::HEIGHT) {
				$winner = $begin;
				break;
			}
		}

		return $winner;
	}

}