<?php
namespace AppBundle\Controller;

use AppBundle\Adapter\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller
{
	/** @var Game */
	protected $gameAdapter;

	protected function initFactory()
	{
		if ($this->gameAdapter === NULL) {
			$this->gameAdapter = new Game($this->get('kernel')->getProjectDir() . DIRECTORY_SEPARATOR . 'db');
		}
	}
}