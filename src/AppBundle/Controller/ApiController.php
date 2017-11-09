<?php

namespace AppBundle\Controller;

use AppBundle\Model\Field;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Model\ResponsePayload;

class ApiController extends AbstractController
{
	/**
	 * @Route("/turn", name="turn")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function turnAction(Request $request)
	{
		$this->initFactory();
		$data = new ResponsePayload();

		$playerId = $request->request->get('playerId');
		$gameId = $request->request->get('gameId');
		$cellId = $request->request->get('cellId');

		$game = $this->gameAdapter->findByGamePlayer($gameId, $playerId);
		if (empty($game)) {
			return $this->getResponse($data->setMessage('Not found.'));
		}

		$winner = $game->getWinner();

		if ($winner) {
			$data->setMessage('We got winner!')
				->setFinished(TRUE);
			return $this->getResponse($data->setMessage('We got winner!')
				->setFinished(TRUE)
			);
		}

		if ($game && $game->getWaiting() !== $playerId) {
			return $this->getResponse($data->setMessage('Not your turn.'));
		}

		$cellH = NULL;
		$cellW = NULL;

		if (!preg_match('/^cell_(\d+)_(\d+)$/', $cellId, $matches)) {
			return $this->getResponse($data->setMessage('Invalid cell ID.'));
		} else {
			$cellH = $matches[1];
			$cellW = $matches[2];
		}

		$field = $game->getField()->getCurrent();
		if (!array_key_exists($cellH, $field) || !array_key_exists($cellW, $field[$cellH])) {
			return $this->getResponse($data->setMessage('Cell item out of bounds.'));
		} else {
			if ($field[$cellH][$cellW] !== NULL) {
				return $this->getResponse($data->setMessage('Item already filled.'));
			}
		}

		$playerSymbol = $game->getPlayerSymbol($playerId);
		$field[$cellH][$cellW] = $playerSymbol;
		$game->changeWaiting();

		$fieldInstance = new Field();
		$fieldInstance->setCurrent($field);

		$game->setField($fieldInstance);
		$game->save();

		$data->setSuccess(TRUE)
			->setField($game->getField()->getCurrent())
			->setWaiting($game->getWaiting());

		return $this->getResponse($data);
	}

	/**
	 * @Route("/status", name="status")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function statusAction(Request $request)
	{
		$this->initFactory();
		$data = new ResponsePayload();

		$playerId = $request->request->get('playerId');
		$gameId = $request->request->get('gameId');

		$game = $this->gameAdapter->findByGamePlayer($gameId, $playerId);
		if (empty($game)) {
			return $this->getResponse($data->setMessage('Not found.'));
		}

		$data->setSuccess(TRUE)
			->setFinished($game->getWinner() ? TRUE : FALSE)
			->setField($game->getField()->getCurrent())
			->setWaiting($game->getWaiting())
			->setWinner($game->getWinner());

		return $this->getResponse($data);
	}

	/**
	 * @param ResponsePayload $data
	 * @return Response
	 */
	private function getResponse(ResponsePayload $data)
	{
		$response = new Response(json_encode($data->toArray()));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}

}
