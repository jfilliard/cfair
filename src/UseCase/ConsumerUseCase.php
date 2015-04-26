<?php

namespace CFair\UseCase;

interface ConsumerUseCase {

	/**
	 * consume a message for future processing
	 * @param  Array  $message the message content
	 * @return int             the new job id
	 */
	public function consume(Array $message);
}
