<?php
namespace AppBundle\Factory;

use AppBundle\Helper\Database;
use AppBundle\Model\Game;

class GameFactory
{
	const GAME_EXPIRE_SECONDS = 3600;

	/** @var Database */
	private $db;

	/** @var string */
	private $databasePath;

	public function __construct($databasePath)
	{
		$this->databasePath = $databasePath;
		$this->db = new Database(new Game(), $databasePath);
	}

	/**
	 * @param $playerId
	 * @return Game
	 */
	public function pickUpOrCreate($playerId)
	{
		$game = NULL;
		foreach($this->db->fetchAll() as $game) {
			if ($game->hasPlayer($playerId)) {
				break;
			}
			if ($game->getPlayersCount() >= Game::MAX_PLAYERS_COUNT) {
				$game = NULL;
				continue;
			}
			break;
		}

		if ($game === NULL) {
			$game = $this->newGame();
		}

		return $game;
	}

	/**
	 * @param string $gameId
	 * @param string $playerId
	 * @return Game|null
	 */
	public function findByGamePlayer($gameId, $playerId)
	{
		$game = NULL;
		foreach($this->db->fetchAll() as $game) {
			if ($game->getId() !== $gameId) {
				$game = NULL;
				continue;
			}
			if (!$game->hasPlayer($playerId)) {
				$game = NULL;
				continue;
			} else {
				break;
			}
		}
		return $game;
	}

	public function cleanupGames()
	{
		$expireTime = new \DateTime();
		$expireTime->modify('- ' . self::GAME_EXPIRE_SECONDS . ' second');
		foreach($this->db->fetchAll() as $game) {
			if ($game->getUpdated() < $expireTime) {
				$game->delete();
				$game = NULL;
				continue;
			}
		}
	}

	public function newGame()
	{
		return $this->db->insert();
	}
}