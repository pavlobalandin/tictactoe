<?php
namespace AppBundle\Model;

class ResponsePayload
{
	private $success = FALSE;
	private $message = NULL;
	private $finished = FALSE;
	private $waiting = NULL;
	private $field = NULL;
	private $winner = NULL;

	/**
	 * @param bool $status
	 * @return ResponsePayload
	 */
	public function setSuccess($status)
	{
		$this->success = (bool)$status;
		return $this;
	}

	/**
	 * @param string $message
	 * @return ResponsePayload
	 */
	public function setMessage($message)
	{
		$this->message = $message;
		return $this;
	}

	/**
	 * @param bool $status
	 * @return ResponsePayload
	 */
	public function setFinished($status)
	{
		$this->finished = (bool)$status;
		return $this;
	}

	/**
	 * @param string $waiting
	 * @return ResponsePayload
	 */
	public function setWaiting($waiting)
	{
		$this->waiting = $waiting;
		return $this;
	}

	/**
	 * @param string $winner
	 * @return ResponsePayload
	 */
	public function setWinner($winner)
	{
		$this->winner = $winner;
		return $this;
	}

	/**
	 * @param array $field
	 * @return ResponsePayload
	 */
	public function setField(array $field)
	{
		$this->field = $field;
		return $this;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		$response = [
			'success' => $this->success,
			'finished' => $this->finished,
		];

		foreach(['message', 'waiting', 'field', 'winner'] as $key) {
			if ($this->$key !== NULL) {
				$response[$key] = $this->$key;
			}
		}

		return $response;
	}
}