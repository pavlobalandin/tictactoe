<?php
namespace AppBundle\Controller;

use AppBundle\Factory\GameFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller
{
	/** @var GameFactory */
	protected $gameFactory;

	protected function initFactory()
	{
		if ($this->gameFactory === NULL) {
			$this->gameFactory = new GameFactory($this->get('kernel')->getProjectDir() . DIRECTORY_SEPARATOR . 'db');
		}
	}
}