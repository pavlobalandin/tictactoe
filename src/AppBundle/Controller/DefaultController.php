<?php

namespace AppBundle\Controller;

use AppBundle\Helper\String;
use AppBundle\Model\Game;
use AppBundle\Model\Player;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
	/**
	 * @Route("/", name="homepage")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Request $request)
	{
		return $this->render('default/index.html.twig', [
			'playerId'   => String::uniqid(),
			'slotsCount' => 0,
		]);
	}

	/**
	 * @Route("/game", name="game")
	 */
	public function gameAction(Request $request)
	{
		$this->initFactory();

		$playerId = $request->request->get('playerId');

		/** @var Game $game */
		$game = $this->gameFactory->pickUpOrCreate($playerId);

		$player = new Player($playerId);
		$player->register($game);

		$this->gameFactory->cleanupGames();

		$game->save();

		return $this->render('default/game.html.twig', [
			'gameId'   => $game->getId(),
			'playerId' => $player->getId(),
			'players'  => $game->getPlayers(),
			'field'    => $game->getField()->getCurrent(),
			'symbol'   => $game->getPlayerSymbol($player->getId()),
			'waiting'  => $game->getWaiting() != $player->getId() ? Game::LABEL_OTHER_PARTY : Game::LABEL_YOU,

			'waitingOtherParty' => $game->getWaiting() != $player->getId() ? 'true' : 'false',
		]);
	}
}
