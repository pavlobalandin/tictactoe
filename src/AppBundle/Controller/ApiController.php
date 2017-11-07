<?php

namespace AppBundle\Controller;

use AppBundle\Model\Field;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
		$data = [
			'success'  => FALSE,
			'finished' => FALSE,
		];

		$playerId = $request->request->get('playerId');
		$gameId = $request->request->get('gameId');
		$cellId = $request->request->get('cellId');

		$game = $this->gameFactory->findByGamePlayer($gameId, $playerId);
		if (empty($game)) {
			$data['message'] = 'Not found.';
			return $this->getResponse($data);
		}

		$winner = $game->getWinner();

		if ($winner) {
			$data['message'] = 'We got winner!';
			$data['finished'] = TRUE;
			return $this->getResponse($data);
		}

		if ($game && $game->getWaiting() !== $playerId) {
			$data['message'] = 'Not your turn.';
			return $this->getResponse($data);
		}

		$cellH = NULL;
		$cellW = NULL;

		if (!preg_match('/^cell_(\d+)_(\d+)$/', $cellId, $matches)) {
			$data['message'] = 'Invalid cell ID.';
			return $this->getResponse($data);
		} else {
			$cellH = $matches[1];
			$cellW = $matches[2];
		}

		$field = $game->getField()->getCurrent();
		if (!array_key_exists($cellH, $field) || !array_key_exists($cellW, $field[$cellH])) {
			$data['message'] = 'Cell item out of bounds.';
			return $this->getResponse($data);
		} else {
			if ($field[$cellH][$cellW] !== NULL) {
				$data['message'] = 'Item already filled.';
				return $this->getResponse($data);
			}
		}

		$playerSymbol = $game->getPlayerSymbol($playerId);
		$field[$cellH][$cellW] = $playerSymbol;
		$game->changeWaiting();

		$fieldInstance = new Field();
		$fieldInstance->setCurrent($field);

		$game->setField($fieldInstance);
		$game->save();

		$data['success'] = TRUE;
		$data['field'] = $game->getField()->getCurrent();
		$data['waiting'] = $game->getWaiting();

		return $this->getResponse($data);
	}

	/**
	 * @param array $data
	 * @return Response
	 */
	private function getResponse(array $data)
	{
		$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
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
		$data = [
			'success' => FALSE,
		];

		$playerId = $request->request->get('playerId');
		$gameId = $request->request->get('gameId');

		$game = $this->gameFactory->findByGamePlayer($gameId, $playerId);
		if (empty($game)) {
			$data['message'] = 'Not found.';
			return $this->getResponse($data);
		}

		$data['success'] = TRUE;
		$data['finished'] = $game->getWinner() ? TRUE : FALSE;
		$data['field'] = $game->getField()->getCurrent();
		$data['waiting'] = $game->getWaiting();
		$data['winner'] = $game->getWinner();

		return $this->getResponse($data);
	}
}
